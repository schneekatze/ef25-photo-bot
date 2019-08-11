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
use App\DomainModel\Telegram\Model\OfferPhoto\Step6Model;
use App\Entity\PhotoOffer;
use Psr\Log\LoggerInterface;

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

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ScreenRepositoryInterface $screenRepository,
        OfferPhotoRepositoryInterface $offerPhotoRepository
    ) {
        $this->screenRepository = $screenRepository;
        $this->offerPhotoRepository = $offerPhotoRepository;
    }

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        $offerPhoto = $this->offerPhotoRepository->findPhotoOffer($userMessage->getUserName());

        $dateTimeMap = [
            'Any day' => 0,
            '13th' => 1565654400,
            '14th' => 1565740800,
            '15th' => 1565827200,
            '16th' => 1565913600,
            '17th' => 1566000000,
            '18th' => 1566086400,
            '19th' => 1566172800,
        ];

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
                "Offer a photo. Step *1* of *6*\n\n
                Perfect! Put your event description here. You can send me a photo with description too!",
                new KeyboardCollection([])
            );

            $offerPhoto->setState(PhotoOffer::STATE_DESCRIPTION);
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_DESCRIPTION) {
            $offerPhoto->setState(PhotoOffer::STATE_DATE)
                ->setDescription($userMessage->getText());


            if ($userMessage instanceof PhotoUserMessage) {
                $offerPhoto->setPhoto($userMessage->getPhoto());
            }

            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *2* of *6*\n\n
                Alrighty! Now, when do you want the photo shoot to take place? Please select one of those dates.
                ",
                new KeyboardCollection(array_keys($dateTimeMap))
            );

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_DATE) {
            $selected = trim($userMessage->getText());

            if(!array_key_exists($selected, $dateTimeMap)) {
                $telegramClient->sendMessage(
                    $userMessage->getChatId(),
                    "The " . $selected . " is not correct option! Please chose one from offered selection.",
                    new KeyboardCollection(array_keys($dateTimeMap))
                );

                return null;
            }

            $offerPhoto->setState(PhotoOffer::STATE_TIME)
                ->setTime($dateTimeMap[$userMessage->getText()]);
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *3* of *6*\n\n
                Perfect! Now, what time? E.g. 12:10, or 22:30 or 5:30, or 4:05. In 24h format!
                ",
                new KeyboardCollection([])
            );

            return null;
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_TIME) {
            $userInput = trim($userMessage->getText());
            $matches = [];
            $result = preg_match_all('/^([0-9]{1,2}):([0-9]{2})$/', $userInput, $matches);

            if ($result === 1) {
                $hour = (int) $matches[1][0];
                $minutes = (int)  $matches[2][0];

                if (
                    $hour < 0
                    || $hour > 23
                    || $minutes < 0
                    || $minutes > 59
                ) {
                    $telegramClient->sendMessage(
                        $userMessage->getChatId(),
                        "Your time " . $userMessage->getText() . " doesn't seem to be correct! I'm looking for E.g. 12:10, or 22:30 or 5:30, or 4:05. In 24h format!",
                        new KeyboardCollection([])
                    );

                    return null;
                }

                $offerPhoto->setState(PhotoOffer::STATE_LOCATION)
                    ->setTime($offerPhoto->getTime() + $hour*60*60 + $minutes*60);
                $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

                $telegramClient->sendMessage(
                    $userMessage->getChatId(),
                    "Offer a photo. Step *4* of *6*\n\n
                    Okay. And where would you like the action to take place?
                    ",
                    new KeyboardCollection([])
                );

                return null;
            }

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Your time " . $userMessage->getText() . " doesn't seem to be correct! I'm looking for E.g. 12:10, or 22:30 or 5:30, or 4:05. In 24h format!",
                new KeyboardCollection([])
            );
        }

        if ($offerPhoto->getState() === PhotoOffer::STATE_LOCATION) {
            $offerPhoto->setState(PhotoOffer::STATE_PRICE)
                ->setLocation($userMessage->getText());
            $this->offerPhotoRepository->savePhotoOffer($offerPhoto);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *5* of *6*\n\n
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
                (new Step6Model($offerPhoto))->render(),
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
