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
        return ($this->_preferredFormat == 'csv') ? $this->outputCsv($outputArray, $include_keys) : $this->outputJson($outputArray);
    }

    public function setPreferredFormat($format) {
        return $this->_preferredFormat = $format;
    }

    public function getPreferredFormat($format) {
        return $this->_preferredFormat = $format;
    }

    public function outputCsv($data, $include_header_row = true)
    {
        $csv = fopen('php://temp', 'w+');

        if(!is_array($data)) {
            $data = array($data);
        }

        reset($data);
        $first_key = key($data);

        // make sure our data contains rows instead of an associative object
        if(!is_numeric($first_key)) {
            $data = array($data);
        }


        if ($include_header_row) {
            $first_row_keys = array_keys($data[$first_key]);
            fputcsv($csv, $first_row_keys);
        }

        foreach ($data as $row) {
            if(!is_array($row)) { $row = array($row); }
            fputcsv($csv, $row);
        }

        rewind($csv);
        $csvStr = stream_get_contents($csv);
        fclose($csv);

        return $csvStr;
    }

    public function outputJson($data, $include_header_row = true)
    {
        return json_encode($data);
    }

    public function getData() {
        return $this->_data;
    }

    public function setSkipWrite($flag) {
        return $this->_skipWrite = $flag;
    }



}
