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
        $branches = $this->getRepositoryBranches($repository['id']);
        $feature_branches = $this->getRepositoryFeatureBranches($repository['id']);

        $repository['branch_count'] = count($branches);
        $repository['feature_branch_count'] = count($feature_branches);
        $repository['feature_branch_active_count'] = 0;


        // get feature branch stats
        $application = $this->getApplication();
        $application->setNestedCommand('beanstalk:feature-stats');
        foreach($feature_branches as $branch){
            $arguments = array(
                'command' => $application->getNestedCommand(),
                'repo' => $repository['id'],
                'branch' => $branch['branch']
            );

            if($feature =  $application->callNestedCommand($arguments)) {
                if($feature['task_status'] != 'complete') {
                    // if task status is not complete, flag branch as active
                    $repository['feature_branch_active_count']++;
                }
            }
        }

        // @todo add feature branch aggregate size
        $output->formattedWrite($repository);
    }
}

