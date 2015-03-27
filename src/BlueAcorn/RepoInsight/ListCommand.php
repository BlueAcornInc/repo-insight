<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends Command
{

    protected function configure()
    {
        $this->setName('list')->setDescription('list repositories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('.... repo list test ....');
    }
}

