<?php

namespace App\Presentation\Http\Exception;

class MessageTypeUnknownException extends \RuntimeException
{
    private $chatId;

    public function __construct(int $chatId)
    {
        $this->chatId = $chatId;

        parent::__construct();
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }
}
