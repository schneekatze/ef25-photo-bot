<?php

namespace App\DomainModel\Screen\Manager;

use App\DomainModel\Agenda\Manager\AgendaManager;
use App\DomainModel\Aggregators\Photographer\Manager\PhotographerScreenManager;
use App\DomainModel\Aggregators\Subject\Manager\SubjectScreenManager;
use App\DomainModel\Help\Manager\HelpManager;
use App\DomainModel\ListMyOffers\Manager\ListMyOffersManager;
use App\DomainModel\ListMyRequests\Manager\ListMyRequestsManager;
use App\DomainModel\ListSeekers\Manager\ListSeekersManager;
use App\DomainModel\OfferPhoto\Manager\OfferPhotoManager;
use App\DomainModel\RequestPhoto\Manager\RequestPhotoManager;
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
        PhotographerScreenManager $photographerScreenManager,
        SubjectScreenManager $subjectScreenManager,
        RequestPhotoManager $requestPhotoManager,
        ListMyRequestsManager $listMyRequestsManager,
        ListSeekersManager $listSeekersManager
    ) {
        $this->screenRepository = $screenRepository;

        $this->managers = [
            ManagerInterface::SCREEN_DASHBOARD => $dashboardManager,
            ManagerInterface::SCREEN_OFFER_PHOTO => $offerPhotoManager,
            ManagerInterface::SCREEN_SHOW_MY_OFFERS => $listMyOffersManager,
            ManagerInterface::SCREEN_HELP => $helpManager,
            ManagerInterface::SCREEN_AGENDA => $agendaManager,
            ManagerInterface::SCREEN_AG_PHOTOGRAPHERS=> $photographerScreenManager,
            ManagerInterface::SCREEN_AG_SEEKERS => $subjectScreenManager,
            ManagerInterface::SCREEN_ASK_PHOTO => $requestPhotoManager,
            ManagerInterface::SCREEN_SHOW_MY_SEARCHES => $listMyRequestsManager,
            ManagerInterface::SCREEN_LIST_SEEKERS => $listSeekersManager,
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
