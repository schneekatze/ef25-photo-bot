<?php

namespace App\DomainModel\Telegram\Client;

use App\DomainModel\Telegram\Collection\KeyboardCollection;
use Longman\TelegramBot\Entities\ServerResponse;

interface ClientInterface
{
    public function sendMessage(int $chatId, string $message, KeyboardCollection $keyboardCollection);
    public function sendPhoto(int $chatId, string $message, KeyboardCollection $keyboardCollection);

    public function setWebhook(string $hook): ServerResponse;
    public function unsetWebhook(): ServerResponse;
    public function handle();
}
