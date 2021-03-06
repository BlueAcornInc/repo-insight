<?php
namespace BlueAcorn\RepoInsight;

// require attask
require_once(__DIR__ . '/../lib/attask/src/StreamClient.php');

$application = new Application('RepoInsight','@package_version@');



// register commands

/*
foreach(glob(__DIR__ . '/*Command.php') as $class_filename) {
    $command_class = '\BlueAcorn\RepoInsight\\' . basename($class_filename,'.php');
    $application->add(new $command_class());
}
*/

$application->add(new BeanstalkListCommand());
$application->add(new BeanstalkStatsCommand());
$application->add(new BeanstalkFeatureListCommand());
$application->add(new BeanstalkFeatureStatsCommand());



