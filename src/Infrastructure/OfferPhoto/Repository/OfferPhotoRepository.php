<?php

namespace App\Infrastructure\OfferPhoto\Repository;

use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
use App\Entity\PhotoOffer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class OfferPhotoRepository implements OfferPhotoRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findPhotoOffer(string $userName): PhotoOffer
    {
        $repository = $this->entityManager->getRepository(PhotoOffer::class);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria::expr()->eq('username', $userName))
            ->andWhere($criteria::expr()->notIn('state', [PhotoOffer::STATE_COMPLETE, PhotoOffer::STATE_CANCELLED]))
        ;

        $offers = $repository->matching($criteria);

        if ($offers->count() === 0) {
            return (new PhotoOffer())
                ->setUsername($userName)
                ->setState(PhotoOffer::STATE_NEW)
            ;
        }

        return $offers->first();
    }

    public function findPhotoOffersByUsername(string $userName): Collection
    {
        $repository = $this->entityManager->getRepository(PhotoOffer::class);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria::expr()->eq('username', $userName))
            ->andWhere($criteria::expr()->eq('state', PhotoOffer::STATE_COMPLETE))
        ;

        return $repository->matching($criteria);
    }

    public function savePhotoOffer(PhotoOffer $photoOffer): PhotoOffer
    {
        $this->entityManager->persist($photoOffer);
        $this->entityManager->flush();

        return $photoOffer;
    }
}
