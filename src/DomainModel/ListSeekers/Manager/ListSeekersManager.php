<?php

namespace App\DomainModel\ListSeekers\Manager;

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

class ListSeekersManager implements ManagerInterface
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
        if ($userMessage->getText() === 'Back') {
            return ManagerInterface::SCREEN_AG_PHOTOGRAPHERS;
        }

        $requests = $this->requestPhotoRepository->findAll();
        $text = '';
        $collection = new KeyboardCollection(['Back']);

        if ($requests->count() > 0) {
            $text = 'Here is a list of your current requests'."\n\n";
            /**
             * @var PhotoRequest $request
             */
            foreach ($requests as $i => $request) {
                $j = $i+1;
                $text .= "📥 Request $j of {$requests->count()}\n"
                    . (new PhotoRequestModel($request))->render()
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
            $text = "No photo requests yet.";
            $collection->add('Back');
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
        return self::SCREEN_LIST_SEEKERS;
    }
}
