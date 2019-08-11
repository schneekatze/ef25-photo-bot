<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rych\Random\Random;

/**
 * PhotoOffers
 *
 * @ORM\Table(name="photo_offers", indexes={@ORM\Index(name="idx_photo_offers_state", columns={"state"})})
 * @ORM\Entity
 */
class PhotoOffer
{
    const STATE_NEW = 'new';
    const STATE_INIT = 'init';
    const STATE_DESCRIPTION = 'description';
    const STATE_TIME = 'time';
    const STATE_LOCATION = 'location';
    const STATE_PRICE = 'price';
    const STATE_CONFIRMATION = 'confirmation';
    const STATE_COMPLETE = 'complete';
    const STATE_CANCELLED = 'cancelled';

    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer", nullable=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="code", type="text", length=32, nullable=true)
     */
    private $code;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="text", length=32, nullable=true)
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="username", type="text", nullable=true)
     */
    private $username;

    /**
     * @var int|null
     *
     * @ORM\Column(name="chat_id", type="integer", nullable=true)
     */
    private $chatId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="location", type="text", nullable=true)
     */
    private $location;

    /**
     * @var string|null
     *
     * @ORM\Column(name="time", type="integer", nullable=true)
     */
    private $time;

    /**
     * @var string|null
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $price;

    /**
     * PhotoOffer constructor.
     */
    public function __construct()
    {
        $this->code = (new Random())->getRandomString(6);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return PhotoOffer
     */
    public function setId(?int $id): PhotoOffer
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return PhotoOffer
     */
    public function setCode(?string $code): PhotoOffer
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string|null $state
     * @return PhotoOffer
     */
    public function setState(?string $state): PhotoOffer
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     * @return PhotoOffer
     */
    public function setUsername(?string $username): PhotoOffer
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getChatId(): ?int
    {
        return $this->chatId;
    }

    /**
     * @param int|null $chatId
     * @return PhotoOffer
     */
    public function setChatId(?int $chatId): PhotoOffer
    {
        $this->chatId = $chatId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return PhotoOffer
     */
    public function setDescription(?string $description): PhotoOffer
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     * @return PhotoOffer
     */
    public function setLocation(?string $location): PhotoOffer
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTime(): ?string
    {
        return $this->time;
    }

    /**
     * @param string|null $time
     * @return PhotoOffer
     */
    public function setTime(?string $time): PhotoOffer
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string|null $price
     * @return PhotoOffer
     */
    public function setPrice(?string $price): PhotoOffer
    {
        $this->price = $price;

        return $this;
    }
}
