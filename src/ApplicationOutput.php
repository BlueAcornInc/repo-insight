<?php


namespace BlueAcorn\RepoInsight;

use Symfony\Component\Console\Output\ConsoleOutput;


class ApplicationOutput extends ConsoleOutput
{
    protected $_preferredFormat = 'csv';
    protected $_skipWrite = false;


    // used by nested command calls to return data, bypassing the writer
    // e.g. Application->callNestedCommand($command) will result in
    //   $data being returned without any manipulation

    protected $_data = null;

    public function formattedWrite($data, $include_keys = true) {

        if($this->_skipWrite) {
            return $this->_data = $data;
        }

        return $this->write($this->formatData($data, $include_keys), self::OUTPUT_RAW);
    }


    protected function formatData($outputArray, $include_keys = true)
    {
        return ($this->_preferredFormat == 'csv') ? $this->arrayToCsv($outputArray, $include_keys) : $this->arrayToJSON($outputArray);
    }

    public function setPreferredFormat($format) {
        return $this->_preferredFormat = $format;
    }

    public function getPreferredFormat($format) {
        return $this->_preferredFormat = $format;
    }

    public function arrayToCsv($array, $include_header_row = true)
    {
        $csv = fopen('php://temp', 'w+');

        // make sure we have rows
        if(!is_array($array)) {
            $array = array($array);
        }

        if(!isset($array[0])) {
            $array = array($array);
        }

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

    public function getData() {
        return $this->_data;
    }

    public function setSkipWrite($flag) {
        return $this->_skipWrite = $flag;
    }



}
