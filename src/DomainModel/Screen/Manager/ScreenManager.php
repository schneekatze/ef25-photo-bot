<?php

namespace App\DomainModel\Screen\Manager;

use App\DomainModel\Agenda\Manager\AgendaManager;
use App\DomainModel\Aggregators\Photographer\Manager\PhotographerScreenManager;
use App\DomainModel\Help\Manager\HelpManager;
use App\DomainModel\ListMyOffers\Manager\ListMyOffersManager;
use App\DomainModel\OfferPhoto\Manager\OfferPhotoManager;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;

class ScreenManager
{
    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    private $managers = [];

    public function __construct(
        ScreenRepositoryInterface $screenRepository,

        DashboardManager $dashboardManager,
        OfferPhotoManager $offerPhotoManager,
        ListMyOffersManager $listMyOffersManager,
        HelpManager $helpManager,
        AgendaManager $agendaManager,
        PhotographerScreenManager $photographerScreenManager
    ) {
        $this->screenRepository = $screenRepository;

        $this->managers = [
            ManagerInterface::SCREEN_DASHBOARD => $dashboardManager,
            ManagerInterface::SCREEN_OFFER_PHOTO => $offerPhotoManager,
            ManagerInterface::SCREEN_SHOW_MY_OFFERS => $listMyOffersManager,
            ManagerInterface::SCREEN_HELP => $helpManager,
            ManagerInterface::SCREEN_AGENDA => $agendaManager,
            ManagerInterface::SCREEN_AG_PHOTOGRAPHERS=> $photographerScreenManager,
        ];
    }

    public function determineScreenManager(AbstractUserMessage $message): ManagerInterface
    {
        $screen = $this->screenRepository->findScreen($message->getUserName());

        if ($screen === null) {
            $screen = $this->screenRepository->saveScreen($message->getUserName(), ManagerInterface::SCREEN_DASHBOARD);
        }

        return $this->managers[$screen->getState()];
    }

    public function findScreenManagerFromName(string $name): ManagerInterface
    {
        return $this->managers[$name];
    }
}
