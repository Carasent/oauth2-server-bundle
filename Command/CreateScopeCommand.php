<?php

namespace OAuth2\ServerBundle\Command;

use OAuth2\ServerBundle\Manager\ScopeManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateScopeCommand extends Command
{
    private ScopeManagerInterface $scopeManager;

    public function __construct(ScopeManagerInterface $scopeManager)
    {
        parent::__construct();

        $this->scopeManager = $scopeManager;
    }

    protected function configure()
    {
        $this
            ->setName('OAuth2:CreateScope')
            ->setDescription('Create a scope for use in OAuth2')
            ->addArgument('scope', InputArgument::REQUIRED, 'The scope key/name')
            ->addArgument('description', InputArgument::REQUIRED, 'The scope description used on authorization screen')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->scopeManager->createScope($input->getArgument('scope'), $input->getArgument('description'));
        } catch (\Doctrine\DBAL\DBALException $e) {
            $output->writeln('<fg=red>Unable to create scope ' . $input->getArgument('scope') . '</fg=red>');

            return Command::FAILURE;
        }

        $output->writeln('<fg=green>Scope ' . $input->getArgument('scope') . ' created</fg=green>');

        return Command::SUCCESS;
    }
}
