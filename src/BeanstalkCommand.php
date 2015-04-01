<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BlueAcorn\beanstalkapp\BeanstalkAppAPI;

//@ todo adapter pattern for services
class BeanstalkCommand extends ApplicationCommand
{

    /**
     * @var BlueAcorn\beanstalkapp\BeanstalkAppAPI
     */
    protected $beanstalk = null;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);

        $application = $this->getApplication();
        $config = $application->getConfig();

        // @todo adapter
        $required_keys = array(
            'beanstalk_account',
            'beanstalk_username',
            'beanstalk_token'
        );

        if (count(array_intersect($required_keys, array_keys($config))) != count($required_keys)) {
            throw new \Exception(
                'config file must contain the following definitions; ' . implode(', ', $required_keys));
        }

        $this->beanstalk = new BeanstalkAppAPI($config['beanstalk_account'], $config['beanstalk_username'],
            $config['beanstalk_token']);
    }

    protected function getAllRepos()
    {
        $page = 1;
        $repositories = array();

        while ($page) {

            $repos = $this->beanstalk->find_all_repositories($page);

            if (count($repos)) {

                foreach ($repos as $repo) {

                    $repo = $repo->repository;

                    $repositories[] = array(
                        'id' => $repo->id,
                        'name' => $repo->name,
                        'created' => $repo->created_at,
                        'last_commit' => $repo->last_commit_at,
                        'size' => $repo->storage_used_bytes / 1024,
                        'url' => $repo->repository_url
                    );
                }

                $page ++;
            } else {
                $page = 0;
            }
        }

        return $repositories;
    }
}

