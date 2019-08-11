<?php

namespace App\DomainModel\OfferPhoto\Repository;

use App\Entity\PhotoOffer;
use Doctrine\Common\Collections\Collection;

interface OfferPhotoRepositoryInterface
{
    public function findPhotoOffer(string $userName): PhotoOffer;
    public function findPhotoOffersByUsername(string $userName): Collection;
    public function savePhotoOffer(PhotoOffer $photoOffer): PhotoOffer;
}
