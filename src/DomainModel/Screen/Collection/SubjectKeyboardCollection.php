<?php

namespace App\DomainModel\Screen\Collection;

use App\DomainModel\Telegram\Collection\KeyboardCollection;

class SubjectKeyboardCollection extends KeyboardCollection
{
    public const ASK_FOR_A_PHOTO = 'Ask for a photo!';
    public const MY_SEARCHES = "My Searches.";
    public const TO_THE_DASHBOARD = "To the Dashboard.";

    public function __construct()
    {
        parent::__construct([
            self::ASK_FOR_A_PHOTO,
            self::MY_SEARCHES,
            self::TO_THE_DASHBOARD,
        ]);
    }
}
