<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Rych\Random\Random;

/**
 * PhotoRequests
 *
 * @ORM\Table(name="photo_requests", indexes={@ORM\Index(name="idx_photo_requests_state", columns={"state"})})
 * @ORM\Entity
 */
class PhotoRequest
{
    const STATE_NEW = 'new';
    const STATE_INIT = 'init';
    const STATE_DESCRIPTION = 'description';
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
     * @ORM\Column(name="photo_id", type="text", nullable=true)
     */
    private $photo;

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
     * @return PhotoRequest
     */
    public function setId(?int $id): PhotoRequest
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
     * @return PhotoRequest
     */
    public function setCode(?string $code): PhotoRequest
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
     * @return PhotoRequest
     */
    public function setState(?string $state): PhotoRequest
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
     * @return PhotoRequest
     */
    public function setUsername(?string $username): PhotoRequest
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
     * @return PhotoRequest
     */
    public function setChatId(?int $chatId): PhotoRequest
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
     * @return PhotoRequest
     */
    public function setDescription(?string $description): PhotoRequest
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     * @return PhotoRequest
     */
    public function setPhoto(?string $photo): PhotoRequest
    {
        $this->photo = $photo;

        return $this;
    }
}
