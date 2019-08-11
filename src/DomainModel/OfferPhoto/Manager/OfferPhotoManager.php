<?php

namespace App\DomainModel\OfferPhoto\Manager;

use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\PhotoUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\DomainModel\Telegram\Model\OfferPhoto\Step5Model;
use App\Entity\PhotoOffer;

class OfferPhotoManager implements ManagerInterface
{
    private const LET_US_GET_STARTED = 'Let us get started!';
    private const PUBLISH = 'Publish!';

    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    /**
     * @var OfferPhotoRepositoryInterface
     */
    private $offerPhotoRepository;

    public function __construct(
        ScreenRepositoryInterface $screenRepository,
        OfferPhotoRepositoryInterface $offerPhotoRepository
    ) {
        $this->screenRepository = $screenRepository;
        $this->offerPhotoRepository = $offerPhotoRepository;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        $offerPhoto = $this->offerPhotoRepository->findPhotoOffer($userMessage->getUserName());

        if ($userMessage->getText() === '/dashboard') {
            if ($offerPhoto->getState() !== PhotoOffer::STATE_NEW) {
                $offerPhoto->setState(PhotoOffer::STATE_CANCELLED);
                $this->offerPhotoRepository->savePhotoOffer($offerPhoto);
            }

            return ManagerInterface::SCREEN_DASHBOARD;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_NEW) {
            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Hey there and thank you for offering your services! If you don't feel up to do this anymore, just send me /dashboard command!",
                new KeyboardCollection([self::LET_US_GET_STARTED])
            );

            $offerPhoto->setState(PhotoOffer::STATE_INIT);
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_INIT && $userMessage->getText() === self::LET_US_GET_STARTED) {
            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *1* of *5*\n\n
                Perfect! Put your event description here. You can send me a photo with description too!",
                new KeyboardCollection([])
            );

            $offerPhoto->setState(PhotoOffer::STATE_DESCRIPTION);
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_DESCRIPTION) {
            $offerPhoto->setState(PhotoOffer::STATE_TIME)
                ->setDescription($userMessage->getText());


            if ($userMessage instanceof PhotoUserMessage) {
                $offerPhoto->setPhoto($userMessage->getPhoto());
            }

            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *2* of *5*\n\n
                Alrighty! Now, when do you want the photo shoot to take place? Please send me a date in format like
                `16 August 2019` or `this Tuesday`.
                ",
                new KeyboardCollection([])
            );

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_TIME) {
            $offerPhoto->setState(PhotoOffer::STATE_LOCATION)
                ->setTime(strtotime($userMessage->getText()));
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *3* of *5*\n\n
                Okay. And where would you like the action to take place?
                ",
                new KeyboardCollection([])
            );

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_LOCATION) {
            $offerPhoto->setState(PhotoOffer::STATE_PRICE)
                ->setLocation($userMessage->getText());
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *4* of *5*\n\n
                Gotcha. How much € would you like to get per shot? Or per session? Feel free to explain your pricing policy.
                ",
                new KeyboardCollection([])
            );

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_PRICE) {
            $offerPhoto->setState(PhotoOffer::STATE_CONFIRMATION)
                ->setPrice($userMessage->getText());
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                (new Step5Model($offerPhoto))->render(),
                new KeyboardCollection(['Publish!'])
            );

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_CONFIRMATION && $userMessage->getText() === self::PUBLISH) {
            $offerPhoto->setState(PhotoOffer::STATE_COMPLETE);
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "And it's done. Have fun at the con!",
                new KeyboardCollection()
            );

            return ManagerInterface::SCREEN_DASHBOARD;
        }

        return null;
    }

    public function navigateToTheScreen(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ManagerInterface
    {
        $this->screenRepository->saveScreen($userMessage->getUserName(), self::getScreenName());

        return $this;
    }

    public static function getScreenName(): string
    {
        return self::SCREEN_OFFER_PHOTO;
    }
}
