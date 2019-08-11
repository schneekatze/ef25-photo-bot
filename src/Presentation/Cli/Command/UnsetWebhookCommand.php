<?php

namespace App\Presentation\Cli\Command;

use App\DomainModel\Telegram\Client\ClientInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UnsetWebhookCommand extends Command
{
    /**
     * @var ClientInterface
     */
    private $telegramClient;

    public function __construct(ClientInterface  $telegramClient)
    {
        $this->telegramClient = $telegramClient;

        parent::__construct('snep:webhook:unset');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln('Unsetting a hook for telegram');

            $result = $this->telegramClient->unsetWebhook();
            if ($result->isOk()) {
                $output->write($result->getDescription());
            }
        } catch (TelegramException $e) {
            $output->writeln('Exception occured while unsetting a webhook');
            $output->writeln($e->getMessage());
        }
    }
}
