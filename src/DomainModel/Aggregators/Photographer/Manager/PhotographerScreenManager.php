<?php

namespace  App\DomainModel\Aggregators\Photographer\Manager;

use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
use App\DomainModel\Screen\Collection\DashboardKeyboardCollection;
use App\DomainModel\Screen\Collection\PhotographerKeyboardCollection;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;

class PhotographerScreenManager implements ManagerInterface
{
    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    public function __construct(ScreenRepositoryInterface $screenRepository) {
        $this->screenRepository = $screenRepository;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        if ($userMessage->getText() === PhotographerKeyboardCollection::OFFER_A_PHOTO) {
            return ManagerInterface::SCREEN_OFFER_PHOTO;
        }

        if ($userMessage->getText() === PhotographerKeyboardCollection::MY_OFFERS) {
            return ManagerInterface::SCREEN_SHOW_MY_OFFERS;
        }

        if ($userMessage->getText() === PhotographerKeyboardCollection::TO_THE_DASHBOARD) {
            return ManagerInterface::SCREEN_DASHBOARD;
        }

        if ($userMessage->getText() === PhotographerKeyboardCollection::FIND_PEOPLE) {
            return ManagerInterface::SCREEN_LIST_SEEKERS;
        }

        $telegramClient->sendMessage(
            $userMessage->getChatId(),
            "👋Hello fellow photographer! What would you like me to do for you?",
            new PhotographerKeyboardCollection()
        );

        return null;
    }

    public function navigateToTheScreen(
        AbstractUserMessage $userMessage,
        ClientInterface $telegramClient
    ): ManagerInterface {
        $this->screenRepository->saveScreen($userMessage->getUserName(), self::getScreenName());

        return $this;
    }

    public static function getScreenName(): string
    {
        return self::SCREEN_AG_PHOTOGRAPHERS;
    }
}
