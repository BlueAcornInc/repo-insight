<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class BeanstalkFeatureStatsCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:feature-stats')->setDescription('get feature branch statistics');
        $this->addArgument('repo', InputArgument::REQUIRED, 'Repository Beanstalk ID (int) or URL (git://)');
        $this->addArgument('branch', InputArgument::REQUIRED, 'Branch Name (e.g. features/3333_Desc)');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository($input->getArgument('repo'));
        $branch = $this->getRepository($input->getArgument('branch'));


        if(!$repository || empty($repository['id'])) {
            throw new \Exception('Repository not found!');
        }


        $branches = $this->getRepositoryBranches($repository['id']);
        if(!in_array($branch,$branches)) {
            throw new \Exception('Branch does not appear to belong to this repository');
        }


        // @todo get branch size


        $output->write($this->formattedOutput(array($branch)), $output::OUTPUT_RAW);
    }
}

