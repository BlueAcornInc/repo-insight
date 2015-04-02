<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class BeanstalkListCommand extends BeanstalkCommand
{

    protected function configure()
    {
        $this->setName('beanstalk:list')
            ->setDescription('list all available repositories')
            ->addOption('stats', null, InputOption::VALUE_NONE, 'if set, include additional branch stats. WARNING: fires 2 additional api calls per repo, network intensive!');
           // ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'limit number of repositores returned', 0);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repositories = $this->getAllRepositories();

        // augment repository list with branch stats if requested...
        if ($input->getOption('stats')) {

            $application = $this->getApplication();
            $application->setNestedCommand('beanstalk:stats');

            foreach ($repositories as $repo_id => $repo_columns) {
                $arguments = array(
                    'command' => $application->getNestedCommand(),
                    'repo' => $repo_id
                );

                if($stat_columns = $application->callNestedCommand($arguments)) {
                    $repositories[$repo_id] = array_merge($repo_columns, $stat_columns);
                }
            }
        }


        $output->formattedWrite($repositories);
    }
}

