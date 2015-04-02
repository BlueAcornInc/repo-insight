<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class BeanstalkFeatureListCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:feature-list')->setDescription('get a list of feature branches');
        $this->addArgument('repo', InputArgument::REQUIRED, 'Repository Beanstalk ID (int) or URL (git://)');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository($input->getArgument('repo'));

        if(!$repository || empty($repository['id'])) {
            throw new \Exception('Repository not found!');
        }

        $feature_branches = $this->getRepositoryFeatureBranches($repository['id']);

        $output->formattedWrite($feature_branches);
    }
}

