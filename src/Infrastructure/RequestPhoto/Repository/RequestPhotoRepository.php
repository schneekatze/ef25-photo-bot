<?php

namespace App\Infrastructure\RequestPhoto\Repository;

use App\DomainModel\RequestPhoto\Repository\RequestPhotoRepositoryInterface;
use App\Entity\PhotoRequest;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class RequestPhotoRepository implements RequestPhotoRepositoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function find(string $userName): PhotoRequest
    {
        $repository = $this->entityManager->getRepository(PhotoRequest::class);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria::expr()->eq('username', $userName))
            ->andWhere($criteria::expr()->notIn('state', [PhotoRequest::STATE_COMPLETE, PhotoRequest::STATE_CANCELLED]))
        ;

        $requests = $repository->matching($criteria);

        if ($requests->count() === 0) {
            return (new PhotoRequest())
                ->setUsername($userName)
                ->setState(PhotoRequest::STATE_NEW)
            ;
        }

        return $requests->first();
    }

    public function findByUsername(string $userName): Collection
    {
        $repository = $this->entityManager->getRepository(PhotoRequest::class);

        $criteria = new \Doctrine\Common\Collections\Criteria();
        $criteria->where($criteria::expr()->eq('username', $userName))
            ->andWhere($criteria::expr()->eq('state', PhotoRequest::STATE_COMPLETE))
        ;

        return $repository->matching($criteria);
    }

    public function save(PhotoRequest $photoRequest): PhotoRequest
    {
        $this->entityManager->persist($photoRequest);
        $this->entityManager->flush();

        return $photoRequest;
    }
}
