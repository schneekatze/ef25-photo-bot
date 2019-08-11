<?php

namespace App\DomainModel\ListMyRequests\Manager;

use App\DomainModel\RequestPhoto\Repository\RequestPhotoRepositoryInterface;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\DomainModel\Telegram\Model\PhotoRequestModel;
use App\Entity\PhotoRequest;
use Longman\TelegramBot\ChatAction;

class ListMyRequestsManager implements ManagerInterface
{
    /**
     * @var ScreenRepositoryInterface
     */
    private $screenRepository;

    /**
     * @var RequestPhotoRepositoryInterface
     */
    private $requestPhotoRepository;

    public function __construct(
        ScreenRepositoryInterface $screenRepository,
        RequestPhotoRepositoryInterface $requestPhotoRepository
    ) {
        $this->screenRepository = $screenRepository;
        $this->requestPhotoRepository = $requestPhotoRepository;
    }

    /**
     * @param TextUserMessage $userMessage
     */
    public function invoke(AbstractUserMessage $userMessage, ClientInterface $telegramClient): ?string
    {
        if ($userMessage->getText() === 'Back' || $userMessage->getText() === '/back') {
            return ManagerInterface::SCREEN_AG_SEEKERS;
        }

        $collection = new KeyboardCollection(['Back']);

        $requests = $this->requestPhotoRepository->findByUsername($userMessage->getUserName());

        if (strpos($userMessage->getText(), '/delete') === 0) {
            $code = substr($userMessage->getText(), 7);
            $filteredOffers = $requests->filter(function (PhotoRequest $photoOffer) use ($code) {
                    return $photoOffer->getCode() === $code;
                });

            if ($filteredOffers->count() === 1) {
                $request = $filteredOffers->first();
                $request->setState(PhotoRequest::STATE_CANCELLED);

                $this->requestPhotoRepository->save($request);

                $telegramClient->sendMessage(
                    $userMessage->getChatId(),
                    'Oki, deleted the request `' . $code . '`.',
                    $collection
                );

                return null;
            }

            $telegramClient->sendMessage(
                $userMessage->getChatId(),
                'Oh, I did not find the request `' . $code . '`. Maybe you already deleted it?',
                $collection
            );

            return null;
        }

        $text = '';

        if ($requests->count() > 0) {
            $text = 'Here is a list of your current requests. Send me /back if you want get back.'."\n\n";
            /**
             * @var PhotoRequest $request
             */
            foreach ($requests as $i => $request) {
                $j = $i+1;
                $text .= "📥 Request $j of {$requests->count()}\n"
                    . (new PhotoRequestModel($request, true))->render()
                    . "\n\n\n"
                ;

                if ($request->getPhoto() !== null) {
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
                        $request->getPhoto(),
                        $collection
                    );

                    $text = '';
                }
            }

            $text .= "\n";
        } else {
            $text = "You didn't request any photos yet.";
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
        return self::SCREEN_SHOW_MY_SEARCHES;
    }
}
