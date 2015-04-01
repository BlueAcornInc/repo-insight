<?php

namespace BlueAcorn\RepoInsight;


use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\ConsoleEvents;


class Application extends BaseApplication {

    protected $_config = array();
    const DEFAULT_CONFIG_FILE = '.repo-insight.yml';


    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        // add global options
        $this->getDefinition()->addOption(new InputOption('--config-file', '-c', InputOption::VALUE_OPTIONAL, 'Path to configuration file containing service credentials. Defaults to <cwd>/' . self::DEFAULT_CONFIG_FILE . ' , falls back to ~/' . self::DEFAULT_CONFIG_FILE));

        // set global event dispatcher
        //  @todo, we could probably use Application::run vs. an event??
        /*
        $dispatcher = new EventDispatcher();
        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            $config_file = $event->getInput()->getParameterOption('--config-file',self::DEFAULT_CONFIG_FILE);
            $application = $event->getCommand()->getApplication();

            // parse config_file
            if(!is_file($config_file)) {
                throw new \Exception('could not find ' . $config_file);
            }
            $config = Yaml::parse($config_file);
            $application->setConfig($config);
        });


        $this->setDispatcher($dispatcher);
        */
    }

    public function getConfig() {
        return $this->_config;
    }

    public function setConfig(Array $config) {
        return $this->_config = $config;
    }


}