<?php

namespace App\Presentation\Cli\Command;

use App\DomainModel\Telegram\Client\ClientInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetWebhookCommand extends Command
{
    /**
     * @var ClientInterface
     */
    private $telegramClient;

    public function __construct(ClientInterface  $telegramClient)
    {
        $this->telegramClient = $telegramClient;

        parent::__construct('snep:webhook:set');
    }

    protected function configure()
    {
        $this->addArgument(
            'hook',
            InputArgument::REQUIRED,
            'The web hook url'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $output->writeln(sprintf('Setting %s as a hook for telegram', $input->getArgument('hook')));

            $result = $this->telegramClient->setWebhook($input->getArgument('hook'));
            if ($result->isOk()) {
                $output->write($result->getDescription());
            }
        } catch (TelegramException $e) {
            $output->writeln('Exception occured while setting a webhook');
            $output->writeln($e->getMessage());
        }
    }
}
