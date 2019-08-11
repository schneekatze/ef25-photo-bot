<?php

namespace App\DomainModel\ListMyOffers\Manager;

use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\DomainModel\Telegram\Model\ViewOfferModel;
use App\Entity\PhotoOffer;

class ListMyOffersManager implements ManagerInterface
{
    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    /**
     * @var OfferPhotoRepositoryInterface
     */
    private $offerPhotoRepository;

    public function __construct(
        ScreenRepositoryInterface $screenRepository,
        OfferPhotoRepositoryInterface $offerPhotoRepository
    ) {
        $this->screenRepository = $screenRepository;
        $this->offerPhotoRepository = $offerPhotoRepository;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        if ($userMessage->getText() === 'Back.' || $userMessage->getText() === '/back') {
            return ManagerInterface::SCREEN_AG_PHOTOGRAPHERS;
        }

        $offers = $this->offerPhotoRepository->findPhotoOffersByUsername($userMessage->getUserName());

        if (strpos($userMessage->getText(), '/delete') === 0) {
            $code = substr($userMessage->getText(), 7);
            $filteredOffers = $offers->filter(function (PhotoOffer $photoOffer) use ($code) {
                    return $photoOffer->getCode() === $code;
                });

            if ($filteredOffers->count() === 1) {
                $offer = $filteredOffers->first();
                $offer->setState(PhotoOffer::STATE_CANCELLED);

                $this->offerPhotoRepository->savePhotoOffer($offer);

                $telegramClient->sendMessage(
                    $userMessage->getChatId(),
                    'Oki, deleted the offer `' . $code . '`.',
                    new KeyboardCollection()
                );

                return null;
            }

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                'Oh, I did not find the offer `' . $code . '`. Maybe you already deleted it?',
                new KeyboardCollection()
            );

            return null;
        }

        $text = '';
        $collection = new KeyboardCollection();

        if ($offers->count() > 0) {
            $text = 'Here is a list of your current offers. Send me /back if you want get back.'."\n\n";
            foreach ($offers as $i => $offer) {
                $j = $i+1;
                $text .= "📤 Offer $j of {$offers->count()}\n"
                    . (new ViewOfferModel($offer, true))->render()
                    . "\n\n\n"
                ;

                if ($offer->getPhoto() !== null) {
                    $telegramClient->sendChatAction($userMessage->getChatId(), ChatAction::TYPING);
                    usleep(5 * 100000);

                    $telegramClient->sendMessage(
                        $userMessage->getChatId(),
                        $text,
                        $collection
                    );

                    $telegramClient->sendChatAction($userMessage->getChatId(), ChatAction::UPLOAD_PHOTO);
                    sleep(1);

                    $telegramClient->sendPhoto(
                        $userMessage->getChatId(),
                        $offer->getPhoto(),
                        $collection
                    );

                    $text = '';
                }
            }

            $text .= "\n";
        } else {
            $text = "You didn't add any offers yet.";
            $collection->add('Back.');
        }

        if ($text !== '') {
            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                $text,
                $collection
            );
        }

        return null;
    }

    public function navigateToTheScreen(
        AbstractUserMessage $userMessage,
        ClientInterface $telegramClient
    ): ManagerInterface {
        $this->screenRepository->saveScreen($userMessage->getUserName(), self::getScreenName());

        return $this;
    }

    public static function getScreenName(): string
    {
        return self::SCREEN_SHOW_MY_OFFERS;
    }
}
