<?php

namespace App\DomainModel\Agenda\Manager;

use App\DomainModel\OfferPhoto\Repository\OfferPhotoRepositoryInterface;
use App\DomainModel\Screen\Manager\ManagerInterface;
use App\DomainModel\Screen\Repository\ScreenRepositoryInterface;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\DomainModel\Telegram\Model\ViewOfferModel;
use Longman\TelegramBot\ChatAction;

class AgendaManager implements ManagerInterface
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
        if ($userMessage->getText() === 'Back') {
            return ManagerInterface::SCREEN_DASHBOARD;
        }

        $map = [
            'Any day' => [
                0,
                1
            ],
            '13th' => [
                1565654400,
                1565740799,
            ],
            '14th' => [
                1565740800,
                1565827199,
            ],
            '15th' => [
                1565827200,
                1565913599,
            ],
            '16th' => [
                1565913600,
                1565999999,
            ],
            '17th' => [
                1566000000,
                1566086399,
            ],
            '18th' => [
                1566086400,
                1566172799,
            ],
            '19th' => [
                1566172800,
                1566259199,
            ],
        ];

        $text = "🗓 Please select a day on which you want to list the offers.\n"
            . "You can find photographers' offers that are ready for either any day or only specific."
        ;

        $collection = new KeyboardCollection([
            'Any day',
            '13th',
            '14th',
            '15th',
            '16th',
            '17th',
            '18th',
            '19th',
            'Back',
        ]);

        if (in_array($userMessage->getText(), array_keys($map))) {
            $interval = $map[$userMessage->getText()];
            $offers = $this->offerPhotoRepository->findInInterval($interval[0], $interval[1]);

            if ($offers->count() > 0) {

                $text .= "\n\n";

                foreach ($offers as $i => $offer) {
                    $j = $i + 1;
                    $text .= "📤 Offer $j of {$offers->count()}\n"
                        .(new ViewOfferModel($offer))->render()
                        ."\n\n\n";

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

                    if (strlen($text) > 2048) {
                        $telegramClient->sendChatAction($userMessage->getChatId(), ChatAction::TYPING);
                        usleep(5 * 1000000);

                        $telegramClient->sendMessage(
                            $userMessage->getChatId(),
                            $text,
                            $collection
                        );
                        sleep(1);

                        $text = '';
                    }
                }
            } else {
                $text = 'No offers yet specifically on ' . $userMessage->getText() . '. Try another option!';
            }
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
        return self::SCREEN_AGENDA;
    }
}
