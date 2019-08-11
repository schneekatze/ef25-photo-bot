<?php

namespace App\DomainModel\Screen\Repository;

use App\Entity\Screen;

interface ScreenRepositoryInterface
{
    public function findScreen(string $userName): ?Screen;
    public function saveScreen(string $userName, string $name): Screen;
}
