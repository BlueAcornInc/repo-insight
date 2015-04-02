<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BeanstalkFeatureListCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:feature-list')->setDescription('get a list of feature branches');
        $this->addArgument('repo', InputArgument::REQUIRED, 'Repository Beanstalk ID (int) or URL (git://)');
        $this->addOption('stats', null, InputOption::VALUE_NONE, 'if set, include additional task-related stats. fires 1 additional api call per branch, network intensive!');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->getRepository($input->getArgument('repo'));

        if(!$repository || empty($repository['id'])) {
            throw new \Exception('Repository not found!');
        }

        $feature_branches = $this->getRepositoryFeatureBranches($repository['id']);



        // augment repository list with branch stats if requested...
        if ($input->getOption('stats')) {

            $application = $this->getApplication();
            $application->setNestedCommand('beanstalk:feature-stats');

            foreach ($feature_branches as $key => $branch) {
                $arguments = array(
                    'command' => $application->getNestedCommand(),
                    'repo' => $repository['id'],
                    'branch' => $branch['branch']
                );

                if($stat_columns = $application->callNestedCommand($arguments)) {
                    $feature_branches[$key] = array_merge($branch, $stat_columns);
                }
            }
        }

        $output->formattedWrite($feature_branches);
    }
}

