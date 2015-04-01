<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class BeanstalkListCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:list')->setDescription('list all available repositories');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repositories = $this->getAllRepositories();
        $output->write($this->formattedOutput($repositories), $output::OUTPUT_RAW);
    }
}

