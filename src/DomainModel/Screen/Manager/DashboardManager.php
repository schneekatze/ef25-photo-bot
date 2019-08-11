<?php

namespace App\DomainModel\Screen\Manager;

use App\DomainModel\OfferPhoto\Manager\OfferPhotoManager;
use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
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

        $offerQuantity = $this->offerPhotoRepository->count();

        $offerFormattedText = 'We don\'t have any offers from photographers yet. Be first?  😉';
        if ($offerQuantity > 0) {
            $offerFormattedText = "P.S. So far we have "
            . $offerQuantity
            . " open offer"
            . ($offerQuantity > 1 ? 's' : '')
            . " from photographer"
            . ($offerQuantity > 1 ? 's' : '')
            ."! You can find those by clicking \"Show me Agenda.\" 😉";
        }

        $telegramClient->sendMessage(
            $userMessage->getChatId(),
            "👋Welcome to *Eurofurence25 Photo Bot*📸!\n"
            . "My purpose is to connect photographers and people who want photos. I'm no how affiliated with EF!\n\n"
            . "So, let's get started! Select one of 4 options below. 🙂\n\n"
            . $offerFormattedText,
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
