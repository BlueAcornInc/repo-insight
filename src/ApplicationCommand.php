<?php
namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Yaml\Yaml;

//@ todo adapter pattern for services
class ApplicationCommand extends Command
{

    protected $format = null;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $config_filename = $input->getParameterOption('--config-file', Application::DEFAULT_CONFIG_FILE);
        $application = $this->getApplication();

        $config_file = $config_filename;

        if (! is_file($config_file)) {
            $config_file = getcwd() . '/' . $config_filename;
            if (! is_file($config_file)) {
                $config_file = getenv('HOME') . '/' . $config_filename;
            }
        }

        // parse config_file
        if (! is_file($config_file)) {
            throw new \Exception('CONFIG FILE NOT FOUND: ' . $config_file . ' does not exist.');
        }
        $config = Yaml::parse($config_file);
        $application->setConfig($config);

        // set format
        $this->format = $input->getArgument('format');
    }

    protected function configure()
    {
        $this->addArgument('format', InputArgument::OPTIONAL, 'Output Format (csv|json)', 'csv');
    }

    // utility helpers
    //////////////////
    protected function formattedOutput($outputArray, $include_array_keys = true)
    {
        $application = $this->getApplication();
        return ($this->format == 'csv') ? $this->arrayToCsv($outputArray, $include_array_keys) : $this->arrayToJSON($outputArray);
    }

    public function arrayToCsv($array, $include_header_row = true)
    {
        $csv = fopen('php://temp', 'w+');

        if ($include_header_row) {
            $first_row_keys = array_keys($array[0]);
            fputcsv($csv, $first_row_keys);
        }

        foreach ($array as $row) {
            if(!is_array($row)) { $row = array($row); }
            fputcsv($csv, $row);
        }

        rewind($csv);
        $csvStr = stream_get_contents($csv);
        fclose($csv);

        return $csvStr;
    }

    public function arrayToJSON($array, $include_header_row = true)
    {
        return json_encode($array);
    }
}

