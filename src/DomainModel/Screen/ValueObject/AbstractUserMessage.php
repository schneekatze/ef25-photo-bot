<?php


namespace App\DomainModel\Screen\ValueObject;


abstract class AbstractUserMessage
{
    private $chatId;
    private $userName;

    public function __construct(string $chatId, string $userName)
    {
        $this->chatId = $chatId;
        $this->userName = $userName;
    }

    public function getChatId(): string
    {
        return $this->chatId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }
}
