<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class BeanstalkListCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:list')->setDescription('list all available repositories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $repositories = $this->getAllRepos();
        $application = $this->getApplication();


        $csvStr = $application->arrayToCSV($repositories);

        $output->write($csvStr,$output::OUTPUT_RAW);
    }
}

