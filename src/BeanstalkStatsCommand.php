<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class BeanstalkStatsCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:stats')->setDescription('list all available repositories');
        $this->addArgument('repo', InputArgument::REQUIRED, 'Repository Beanstalk ID (int) or URL (git://)');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository($input->getArgument('repo'));

        if(!$repository || empty($repository['id'])) {
            throw new \Exception('Repository not found!');
        }


        // add additional stats
        $response = $this->beanstalk->find_repository_branches($repository['id']);

        $repository['branch_count'] = count($this->getRepositoryBranches($repository['id']));
        $repository['feature_branch_count'] = count($this->getRepositoryFeatureBranches($repository['id']));
        $repository['feature_branch_active_count'] = 0;


        // @todo add feature branch aggregate size
        $output->formattedWrite($repository);
    }
}

