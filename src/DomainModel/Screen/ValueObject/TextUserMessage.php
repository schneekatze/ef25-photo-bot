<?php

namespace App\DomainModel\Screen\ValueObject;

class TextUserMessage extends AbstractUserMessage
{
    private $text;

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }
}
