<?php

namespace App\DomainModel\Screen\ValueObject;

class PhotoUserMessage extends AbstractUserMessage
{
    private $text;
    private $photo;

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;

        return $this;
    }

    public function getPhoto(): string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo)
    {
        $this->photo = $photo;

        return $this;
    }
}
