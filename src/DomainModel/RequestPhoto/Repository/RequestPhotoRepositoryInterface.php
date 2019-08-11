<?php

namespace App\DomainModel\RequestPhoto\Repository;

use App\Entity\PhotoRequest;
use Doctrine\Common\Collections\Collection;

interface RequestPhotoRepositoryInterface
{
    public function find(string $userName): PhotoRequest;
    public function findByUsername(string $userName): Collection;
    public function save(PhotoRequest $photoRequest): PhotoRequest;
}
