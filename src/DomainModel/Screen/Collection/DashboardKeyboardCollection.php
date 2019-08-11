<?php

namespace App\DomainModel\Screen\Collection;

use App\DomainModel\Telegram\Collection\KeyboardCollection;

class DashboardKeyboardCollection extends KeyboardCollection
{
    public const AGENDA = "Show me Agenda.";
    public const FOR_PHOTOGRAPHERS = 'I am a photographer!';
    public const FOR_SUBJECTS = 'I want a photo of me!';
    public const HELP = "Help.";

    public function __construct()
    {
        parent::__construct([
            self::AGENDA,
            self::FOR_PHOTOGRAPHERS,
            self::FOR_SUBJECTS,
            self::HELP,
        ]);
    }
}
