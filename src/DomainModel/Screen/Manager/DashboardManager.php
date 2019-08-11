<?php

namespace App\DomainModel\Screen\Manager;

use App\DomainModel\OfferPhoto\Manager\OfferPhotoManager;
use App\DomainModel\Screen\Collection\DashboardKeyboardCollection;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;

class DashboardManager implements ManagerInterface
{
    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    public function __construct(ScreenRepositoryInterface $screenRepository)
    {
        $this->screenRepository = $screenRepository;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        if ($userMessage->getText() === DashboardKeyboardCollection::FOR_PHOTOGRAPHERS) {
            return ManagerInterface::SCREEN_AG_PHOTOGRAPHERS;
        }

        if ($userMessage->getText() === DashboardKeyboardCollection::HELP) {
            return ManagerInterface::SCREEN_HELP;
        }

        if ($userMessage->getText() === DashboardKeyboardCollection::AGENDA) {
            return ManagerInterface::SCREEN_AGENDA;
        }

        if ($userMessage->getText() === DashboardKeyboardCollection::FOR_SUBJECTS) {
            return ManagerInterface::SCREEN_AG_SEEKERS;
        }

        $telegramClient->sendMessage(
            $userMessage->getChatId(),
            "👋Welcome to *Eurofurence25 Photo Bot*📸!\n"
            . "My purpose is to connect photographers and their subjects. I'm no how affiliated with EF!\n\n"
            . "So, let's get started! Select one of 4 options below. 🙂",
            new DashboardKeyboardCollection()
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
        return self::SCREEN_DASHBOARD;
    }
}
