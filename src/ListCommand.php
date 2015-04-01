<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use BlueAcorn\beanstalkapp\BeanstalkAppAPI;


//@ todo adapter pattern for services

class ListCommand extends Command
{

    protected function configure()
    {
        $this->setName('repo:list')->setDescription('list all available repositories');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $config = $this->getApplication()->getConfig();
        $beanstalk = new BeanstalkAppAPI($config['beanstalk_account'],$config['beanstalk_username'],$config['beanstalk_token']);
        $output->writeln('.... repo list test ....');


        $repositories = $beanstalk->find_all_repositories();
        var_dump($repositories);

    }
}

