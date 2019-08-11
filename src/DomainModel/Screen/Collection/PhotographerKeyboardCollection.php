<?php

namespace App\DomainModel\Screen\Collection;

use App\DomainModel\Telegram\Collection\KeyboardCollection;

class PhotographerKeyboardCollection extends KeyboardCollection
{
    public const OFFER_A_PHOTO = 'I want to offer a photo set!';
    public const FIND_PEOPLE = "Find people looking for photos!";
    public const MY_OFFERS = "Show me my photo set offers.";
    public const TO_THE_DASHBOARD = "To the Dashboard.";

    public function __construct()
    {
        parent::__construct([
            self::OFFER_A_PHOTO,
            self::FIND_PEOPLE,
            self::MY_OFFERS,
            self::TO_THE_DASHBOARD,
        ]);
    }
}
