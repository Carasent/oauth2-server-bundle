<?php

namespace OAuth2\ServerBundle\Command;

use OAuth2\ServerBundle\Manager\ClientManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateClientCommand extends Command
{
    private ClientManager $clientManager;

    public function __construct(ClientManager $clientManager)
    {
        parent::__construct();

        $this->clientManager = $clientManager;
    }

    protected function configure(): void
    {
        $this
            ->setName('OAuth2:CreateClient')
            ->setDescription('Create a OAuth2 client')
            ->addArgument('identifier', InputArgument::REQUIRED, 'The client identifier')
            ->addArgument('redirect_uri', InputArgument::REQUIRED, 'The client redirect uris (comma separated)')
            ->addArgument('grant_types', InputArgument::OPTIONAL, 'Grant types to restrict the client to (comma separated)')
            ->addArgument('scopes', InputArgument::OPTIONAL, 'Scopes to restrict the client to (comma separated)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $client = $this->clientManager->createClient(
                $input->getArgument('identifier'),
                explode(',', $input->getArgument('redirect_uri')),
                explode(',', $input->getArgument('grant_types')),
                explode(',', $input->getArgument('scopes'))
            );
        } catch (\Doctrine\DBAL\DBALException $e) {
            $output->writeln('<fg=red>Unable to create client ' . $input->getArgument('identifier') . '</fg=red>');
            $output->writeln('<fg=red>' . $e->getMessage() . '</fg=red>');

            return 1;
        } catch (\OAuth2\ServerBundle\Exception\ScopeNotFoundException $e) {
            $output->writeln('<fg=red>Scope not found, please create it first</fg=red>');

            return 1;
        }

        $output->writeln('<fg=green>Client ' . $input->getArgument('identifier') . ' created with secret ' . $client->getClientSecret() . '</fg=green>');

        return Command::SUCCESS;
    }
}
