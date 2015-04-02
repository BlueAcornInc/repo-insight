<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Input\ArrayInput;

class Application extends BaseApplication
{

    const DEFAULT_CONFIG_FILE = '.repo-insight.yml';
    const FEATURE_BRANCH_PATTERN = '/^features?\//i';

    /**
     * @var array Yaml Config
     */
    protected $_config = array();


    /**
     * @var string|boolean used when calling commands internally
     */
    protected $_nestedCommand = false;


    /**
     * @var array registry of REST API services &c
     */
    protected $_services = array();


    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        parent::__construct($name, $version);

        // add global options
        $this->getDefinition()->addOption(
            new InputOption('--config-file', '-c', InputOption::VALUE_OPTIONAL,
                'Path to configuration file containing service credentials. Defaults to <cwd>/' .
                     self::DEFAULT_CONFIG_FILE . ' , falls back to ~/' . self::DEFAULT_CONFIG_FILE));

        /*
        // set global event dispatcher
         $dispatcher = new EventDispatcher();
         $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {

             $input = $event->getInput();
             $output = $event->getOutput();

             // set the formatter

             if($input->hasArgument('format')) {
                 var_dump($input->getArgument('format')); die();
                 $output->setPreferredFormat($input->getArgument('format'));
             }

         });
         $this->setDispatcher($dispatcher);
         */
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {

        if($output === null) {
            $output = new ApplicationOutput();
        }

        return parent::run($input, $output);
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function setConfig(Array $config)
    {
        return $this->_config = $config;
    }

    public function setNestedCommand($command)
    {
        return $this->_nestedCommand = $command;
    }

    public function getNestedCommand()
    {
        return $this->_nestedCommand;
    }

    public function isNestedCommand()
    {
        return ($this->_nestedCommand);
    }

    public function callNestedCommand($arguments)
    {
        $command = $this->getNestedCommand();
        $input = new ArrayInput($arguments);
        $output = new ApplicationOutput();

        $output->setSkipWrite(true);

        if($statusCode = $this->find($command)->run($input,$output)) {
            throw new \Exception('nested command > $command returned non-zero status (' . $statusCode . ')');
        }

        return $output->getData();
    }

    public function addService($name, $instance) {
        $this->_services[$name] = $instance;
    }

    public function getService($name) {
        return (isset($this->_services[$name])) ? $this->_services[$name] : null;
    }
}