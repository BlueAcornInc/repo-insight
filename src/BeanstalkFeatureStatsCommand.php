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
        $this->addArgument('branch', InputArgument::REQUIRED, 'Branch Name (e.g. features/3333_Desc) or Workfront ID (e.g. 3333)');

        parent::configure();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $application = $this->getApplication();
        $config = $application->getConfig();

        // @todo adapter
        $required_keys = array(
            'workfront_endpoint',
            'workfront_username',
            'workfront_password'
        );

        if (count(array_intersect($required_keys, array_keys($config))) != count($required_keys)) {
            throw new \Exception(
                'config file must contain the following definitions; ' . implode(', ', $required_keys));
        }

        if(!$application->getService('workfront')) {

            $workfront = new \StreamClient($config['workfront_endpoint']);
            $workfront->login($config['workfront_username'],$config['workfront_password']);
            $application->addService('workfront',$workfront);

        }

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $requested_repo = $input->getArgument('repo');
        $requested_branch = $input->getArgument('branch');


        $repository = $this->getRepository($requested_repo);
        if(!$repository || empty($repository['id'])) {
            throw new \Exception('Repository not found!');
        }

        $branches = $this->getRepositoryFeatureBranches($repository['id']);
        foreach($branches as $branch) {
            if($branch['branch'] == $requested_branch || $branch['task_ref_id'] == $requested_branch) {

                $branch['task_status'] = 'unknown';
                $branch['task_link'] = '';
                $branch['task_percent_complete'] = '';
                $branch['task_planned_complete_date'] = '';


                if(!empty($branch['task_ref_id'])) {
                    $application = $this->getApplication();
                    $workfront = $application->getService('workfront');
                    $tasks = $workfront->search('task',array('referenceNumber' => $branch['task_ref_id']));

                    if($tasks && !empty($tasks)) {
                        foreach($tasks as $task) {
                            $branch['task_status'] = ($task->status == 'CPL') ? 'complete' : 'incomplete';
                            $branch['task_link'] = $application->getConfig()['workfront_endpoint'] . '/task/view?ID=' . $task->ID;
                            $branch['task_percent_complete'] = $task->percentComplete;
                            $branch['task_planned_complete_date'] = $task->plannedCompletionDate;
                        }
                    }
                }

                return $output->formattedWrite($branch);
            }
        }

        throw new \Exception('Branch does not appear to be a feature branch on this repository.');

    }
}

