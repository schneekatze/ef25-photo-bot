<?php

namespace App\Infrastructure\Screen\Repository;

use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\Entity\Screen;
use Doctrine\ORM\EntityManagerInterface;

class ScreenRepository implements ScreenRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findScreen(string $userName): ?Screen
    {
        return $this->entityManager->getRepository(Screen::class)->findOneBy(['username' => $userName]);
    }

    public function saveScreen(string $userName, string $name): Screen
    {
        $screen = $this->findScreen($userName) ?? (new Screen())->setState($name)->setUsername($userName);
        $screen->setLastOpenedAt(time());
        $screen->setState($name);

        $this->entityManager->persist($screen);
        $this->entityManager->flush();

        return $screen;
    }
}
