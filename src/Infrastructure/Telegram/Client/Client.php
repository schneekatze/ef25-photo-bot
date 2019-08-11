<?php

namespace App\Infrastructure\Telegram\Client;

use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Psr\Log\LoggerInterface;

class Client implements ClientInterface
{
    private const BOT_USERNAME = 'EF25PhotosBot';

    /**
     * @var Telegram
     */
    private $telegram;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Client constructor.
     */
    public function __construct(string $telegramToken, LoggerInterface $logger)
    {
        $this->telegram = new Telegram($telegramToken, self::BOT_USERNAME);
        $this->telegram->addCommandsPaths([
            __DIR__ . '/../../../DomainModel/Telegram/Command',
        ]);
        $this->logger = $logger;
    }

    public function sendMessage(int $chatId, string $message, KeyboardCollection $keyboardCollection)
    {
        $this->logger->info('Sending a message to telegram', [
            'chat_id' => $chatId,
            'message' => $message,
        ]);

        $replyMarkup = [];

        if ($keyboardCollection->count() === 0) {
            $replyMarkup["hide_keyboard"] = true;
        } else {
            $replyMarkup["keyboard"] = [
                $keyboardCollection->toArray()
            ];
        }

        return Request::sendMessage([
            'chat_id' => $chatId,
            'text'    => $message,
            'parse_mode'=> 'Markdown',
            'reply_markup'=> $replyMarkup,
        ]);
    }

    public function sendPhoto(int $chatId, string $message, KeyboardCollection $keyboardCollection)
    {
        return Request::sendPhoto([
            'chat_id' => $chatId,
            'photo'   => $message,
        ]);
    }

    public function setWebhook(string $hook): ServerResponse
    {
        return $this->telegram->setWebhook($hook);
    }

    public function unsetWebhook(): ServerResponse
    {
        return $this->telegram->deleteWebhook();
    }

    public function sendChatAction(int $chatId, string $action)
    {
        return Request::sendChatAction([
            'chat_id' => $chatId,
            'action'  => $action,
        ]);
    }
}
