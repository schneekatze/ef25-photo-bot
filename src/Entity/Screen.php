<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Screens
 *
 * @ORM\Table(name="screens")
 * @ORM\Entity
 */
class Screen
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="text", nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="text", length=255, nullable=false)
     */
    private $state;

    /**
     * @var int
     *
     * @ORM\Column(name="last_opened_at", type="integer", nullable=false)
     */
    private $lastOpenedAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Screen
     */
    public function setId(int $id): Screen
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return Screen
     */
    public function setUsername(string $username): Screen
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Screen
     */
    public function setState(string $state): Screen
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return int
     */
    public function getLastOpenedAt(): int
    {
        return $this->lastOpenedAt;
    }

    /**
     * @param int $lastOpenedAt
     * @return Screen
     */
    public function setLastOpenedAt(int $lastOpenedAt): Screen
    {
        $this->lastOpenedAt = $lastOpenedAt;

        return $this;
    }
}
