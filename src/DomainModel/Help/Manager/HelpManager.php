<?php

namespace App\DomainModel\Help\Manager;

use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\DomainModel\Telegram\Model\ViewOfferModel;
use App\Entity\PhotoOffer;

class HelpManager implements ManagerInterface
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
        if ($userMessage->getText() === 'Back to the Dashboard.') {
            return ManagerInterface::SCREEN_DASHBOARD;
        }

        $telegramClient->sendMessage(
            $userMessage->getChatId(),
            "If you experience any sort of technical issues with the bot, please poke @kamsirius.",
            new KeyboardCollection(['Back to the Dashboard.'])
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
        return self::SCREEN_HELP;
    }
}
