<?php

namespace App\DomainModel\Screen\Manager;

use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;

interface ManagerInterface
{
    const SCREEN_DASHBOARD = 'Dashboard';
    const SCREEN_HELP = 'Help';
    const SCREEN_AGENDA = 'Agenda';

    const SCREEN_OFFER_PHOTO = 'OfferPhoto';
    const SCREEN_SHOW_MY_OFFERS = 'ShowMyOffers';

    const SCREEN_ASK_PHOTO = 'AskPhoto';
    const SCREEN_SHOW_MY_SEARCHES = 'PhotoSearches';
    const SCREEN_LSIT_SEEKERS = 'ListSeekers';

    const SCREEN_AG_PHOTOGRAPHERS = 'Photographers';
    const SCREEN_AG_SEEKERS = 'Seekers';

    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string;
    public function navigateToTheScreen(
        AbstractUserMessage $userMessage,
        ClientInterface $telegramClient
    ): ManagerInterface;
    public static function getScreenName(): string;
}
