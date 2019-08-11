<?php

namespace App\Presentation\Http\Controller;

use App\DomainModel\Screen\Collection\DashboardKeyboardCollection;
use App\DomainModel\Screen\Manager\ScreenManager;
use App\DomainModel\Screen\ValueObject\AbstractUserMessage;
use App\DomainModel\Screen\ValueObject\TextUserMessage;
use App\DomainModel\Telegram\Client\ClientInterface;
use App\DomainModel\Telegram\Collection\KeyboardCollection;
use App\Presentation\Http\Exception\MessageTypeUnknownException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhookController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ClientInterface
     */
    private $telegramClient;

    /**
     * @var ScreenManager
     */
    private $screenManager;

    public function __construct(LoggerInterface $logger, ClientInterface $telegramClient, ScreenManager $screenManager)
    {
        $this->logger = $logger;
        $this->telegramClient = $telegramClient;
        $this->screenManager = $screenManager;
    }

    public function index(Request $request)
    {
        $this->logger->info('Incoming webhook', ['request' => $request->getContent()]);

        try {
            $inputData = \GuzzleHttp\json_decode($request->getContent(), true);
            $message = $this->createUserMessage($inputData);
            $screen = $this->screenManager->determineScreenManager($message);
            $newScreenName = $screen->invoke($message, $this->telegramClient);

            if ($newScreenName !== null) {
                $this->screenManager
                    ->findScreenManagerFromName($newScreenName)
                    ->navigateToTheScreen($message, $this->telegramClient)
                    ->invoke($message, $this->telegramClient);
            }
        } catch (MessageTypeUnknownException $exception) {
            $this->telegramClient->sendMessage(
                $exception->getChatId(),
                'I uhm... can accept only text and photo (not yet) messages :<. Sorry!',
                new KeyboardCollection()
            );
        }

        return new Response('', Response::HTTP_ACCEPTED);
    }

    private function createUserMessage(array $inputData): AbstractUserMessage
    {
        if (array_key_exists('text', $inputData['message'])) {
            return (new TextUserMessage(
                $inputData['message']['chat']['id'], $inputData['message']['from']['username']
            ))->setText($inputData['message']['text']);
        }

        throw new MessageTypeUnknownException($inputData['message']['chat']['id']);
    }
}
