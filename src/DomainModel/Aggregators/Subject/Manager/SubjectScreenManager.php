<?php

namespace  App\DomainModel\Aggregators\Subject\Manager;

use App\DomainModel\Screen\Collection\SubjectKeyboardCollection;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;

class SubjectScreenManager implements ManagerInterface
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
        if ($userMessage->getText() === SubjectKeyboardCollection::ASK_FOR_A_PHOTO) {
            return ManagerInterface::SCREEN_ASK_PHOTO;
        }

        if ($userMessage->getText() === SubjectKeyboardCollection::MY_SEARCHES) {
            return ManagerInterface::SCREEN_SHOW_MY_SEARCHES;
        }

        if ($userMessage->getText() === SubjectKeyboardCollection::TO_THE_DASHBOARD) {
            return ManagerInterface::SCREEN_DASHBOARD;
        }

        $telegramClient->sendMessage(
            $userMessage->getChatId(),
            "👋Hello there photography seeker! What would you like me to do for you?",
            new SubjectKeyboardCollection()
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
        return self::SCREEN_AG_SEEKERS;
    }
}
