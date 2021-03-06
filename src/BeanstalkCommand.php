<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use BlueAcorn\RepoInsight\Application;
use BlueAcorn\beanstalkapp\BeanstalkAppAPI;

//@ todo adapter pattern for services
class BeanstalkCommand extends ApplicationCommand
{

    /**
     * @var BlueAcorn\beanstalkapp\BeanstalkAppAPI
     */
    protected $beanstalk = null;

    protected $beanstalk_repositories = null;

    protected $beanstalk_repository_branches = array();

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

        $application->addService('beanstalk',
            new BeanstalkAppAPI($config['beanstalk_account'], $config['beanstalk_username'],
                $config['beanstalk_token']));
    }

    // helper utilities
    ///////////////////


    // @todo improve cache across multiple runs (1min?)
    protected function getAllRepositories()
    {
        if ($this->beanstalk_repositories === null) {
            $beanstalk = $this->getApplication()->getService('beanstalk');
            $page = 1;
            $this->beanstalk_repositories = array();



            while ($page) {

                $response = $beanstalk->find_all_repositories($page);

                if (count($response)) {

                    foreach ($response as $repoResponse) {
                        $repo = $this->buildRepository($repoResponse);

                        if ($repo && ! empty($repo['id'])) {
                            $this->beanstalk_repositories[$repo['id']] = $repo;
                        }
                    }

                    $page ++;
                } else {
                    $page = 0;
                }
            }
        }

        return $this->beanstalk_repositories;
    }

    protected function getRepository($repo_id_or_url)
    {
        if (! is_numeric($repo_id_or_url)) {
            foreach ($this->getAllRepositories() as $repo) {
                if ($repo_id_or_url == $repo['url']) {
                    return $repo;
                }
            }
        }

        if ($this->beanstalk_repositories && ! empty($this->beanstalk_repositories[$repo_id_or_url])) {
            return $this->beanstalk_repositories[$repo_id_or_url];
        }

        $beanstalk = $this->getApplication()->getService('beanstalk');
        $response = $beanstalk->find_single_repository($repo_id_or_url);

        return $this->buildRepository($response);
    }

    protected function getRepositoryBranches($repo_id)
    {
        if (empty($this->beanstalk_repository_branches[$repo_id])) {

            $beanstalk = $this->getApplication()->getService('beanstalk');
            $branches = array();
            $response = $beanstalk->find_repository_branches($repo_id);
            foreach ($response as $branchResponse) {
                $branches[] = $this->buildBranch($branchResponse);
            }

            $this->beanstalk_repository_branches[$repo_id] = $branches;
        }

        return $this->beanstalk_repository_branches[$repo_id];
    }

    protected function getRepositoryFeatureBranches($repo_id)
    {
        $branches = array();

        foreach ($this->getRepositoryBranches($repo_id) as $branch) {
            if ($branch['is_feature'] == 'yes') {
                $branches[] = $branch;
            }
        }

        return $branches;
    }

    private function buildRepository($apiResponse)
    {
        $repo = $apiResponse->repository;

        return array(
            'id' => $repo->id,
            'name' => $repo->name,
            'created' => $repo->created_at,
            'last_commit' => $repo->last_commit_at,
            'size' => $repo->storage_used_bytes / 1024,
            'url' => $repo->repository_url
        );
    }

    private function buildBranch($apiResponse)
    {
        $branch = $apiResponse->branch;
        $feature_pattern = '/^features?\//i';

        $feature = (preg_match(Application::FEATURE_BRANCH_PATTERN, $branch));
        $ref_id = '';
        $description = '';

        if ($feature) {

            $components = explode('_', preg_replace(Application::FEATURE_BRANCH_PATTERN, '', $branch));
            $ref_id_test = array_shift($components);

            if (is_numeric($ref_id_test)) {
                $ref_id = $ref_id_test;
                $description = implode(' ', $components);
            }
        }

        return array(
            'branch' => $branch,
            'is_feature' => ($feature) ? 'yes' : 'no',
            'description' => $description,
            'task_ref_id' => $ref_id
        );
    }
}

