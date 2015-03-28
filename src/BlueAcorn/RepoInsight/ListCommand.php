<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use BlueAcorn\BeanstalkAppApi\BeanstalkAppAPI;
use BlueAcorn\BeanstalkAppApi\BeanstalkAppAPIException;


class ListCommand extends Command
{

    protected function configure()
    {
        $this->setName('list')->setDescription('list repositories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $x = new BeanstalkAppAPI();
        $output->writeln('.... repo list test ....');
    }
}

