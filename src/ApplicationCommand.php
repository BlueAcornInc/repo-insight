<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;



//@ todo adapter pattern for services

class ApplicationCommand extends Command
{

    protected function initialize(InputInterface $input, OutputInterface $output)
    {

        $config_filename = $input->getParameterOption('--config-file',Application::DEFAULT_CONFIG_FILE);
        $application = $this->getApplication();


        $config_file = $config_filename;

        if(!is_file($config_file)) {
            $config_file = getcwd() . '/' . $config_filename;
            if(!is_file($config_file)) {
                $config_file = getenv('HOME') . '/' . $config_filename;
            }
        }


        // parse config_file
        if(!is_file($config_file)) {
            throw new \Exception('CONFIG FILE NOT FOUND: ' . $config_file . ' does not exist.');
        }
        $config = Yaml::parse($config_file);
        $application->setConfig($config);
    }



}

