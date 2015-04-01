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

        if(count(array_intersect($required_keys,array_keys($config))) != count($required_keys)) {
            throw new \Exception('config file must contain the following definitions; ' . implode(', ',$required_keys));
        }


        $this->beanstalk = new BeanstalkAppAPI($config['beanstalk_account'],$config['beanstalk_username'],$config['beanstalk_token']);
    }



}

