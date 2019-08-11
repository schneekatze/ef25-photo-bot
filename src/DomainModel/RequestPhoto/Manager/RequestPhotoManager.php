<?php

namespace App\DomainModel\RequestPhoto\Manager;

use App\DomainModel\RequestPhoto\Repository\RequestPhotoRepositoryInterface;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\DomainModel\Telegram\Model\PhotoRequestModel;
use App\Entity\PhotoRequest;

class RequestPhotoManager implements ManagerInterface
{
    private const LET_US_GET_STARTED = 'Let us get started!';
    private const PUBLISH = 'Publish!';

    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    /**
     * @var RequestPhotoRepositoryInterface
     */
    private $requestPhotoRepository;

    public function __construct(
        ScreenRepositoryInterface $screenRepository,
        RequestPhotoRepositoryInterface $requestPhotoRepository
    ) {
        $this->screenRepository = $screenRepository;
        $this->requestPhotoRepository = $requestPhotoRepository;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        $photoRequest = $this->requestPhotoRepository->find($userMessage->getUserName());

        if ($userMessage->getText() === '/dashboard') {
            if ($photoRequest->getState() !== PhotoRequest::STATE_NEW) {
                $photoRequest->setState(PhotoRequest::STATE_CANCELLED);
                $this->requestPhotoRepository->save($photoRequest);
            }

            return ManagerInterface::SCREEN_DASHBOARD;
        }

        if ($photoRequest->getState() === PhotoRequest::STATE_NEW) {
            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Hey there! Let's find a photographer for you! Ready to start? If you don't feel up to do this anymore, just send me /dashboard command!",
                new KeyboardCollection([self::LET_US_GET_STARTED])
            );

            $photoRequest->setState(PhotoRequest::STATE_INIT);
            $this->requestPhotoRepository->save($photoRequest);

            return null;
        }

        if ($photoRequest->getState() === PhotoRequest::STATE_INIT && $userMessage->getText() === self::LET_US_GET_STARTED) {
            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Request a photo. Step *1* of *2*\n\n
                Perfect! What photo would you like to be taken? Briefly explain your idea here :)",
                new KeyboardCollection([])
            );

            $photoRequest->setState(PhotoRequest::STATE_DESCRIPTION);
            $this->requestPhotoRepository->save($photoRequest);

            return null;
        }

        if ($photoRequest->getState() === PhotoRequest::STATE_DESCRIPTION) {
            $photoRequest->setState(PhotoRequest::STATE_CONFIRMATION)
                ->setDescription($userMessage->getText());
            $this->requestPhotoRepository->save($photoRequest);

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                "Offer a photo. Step *2* of *2*\n\n
                Alrighty! Please check that info you provided is what you wanted to ask for. Once you are confident enough, just press the 'Publish!' button :)\n\n
                " .(new PhotoRequestModel($photoRequest))->render(),
                new KeyboardCollection(['Publish!'])
            );

            return null;
        }

        if ($photoRequest->getState() === PhotoRequest::STATE_CONFIRMATION && $userMessage->getText() === self::PUBLISH) {
            $photoRequest->setState(PhotoRequest::STATE_COMPLETE);
            $this->requestPhotoRepository->save($photoRequest);

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
        return self::SCREEN_ASK_PHOTO;
    }
}
