<?php


class QueryResults
{
    private $results;

    /**
     * QueryResults constructor.
     * @param $results
     */
    public function __construct(&$results)
    {
        $this->results = $results;
    }


    /**
     * @return array Returns the results as an array
     */
    public function asArray(){
        return $this->results;
    }

    /**
     * @return string Returns the results of the query in JSON format
     */
    public function asJSON(){
        return json_encode($this->results);
    }

    /**
     * @return string Returns the results of the query in CSV format
     */
    public function asCSV(){
        $result = $this->results;
        $outputBuffer = fopen("php://output", 'w');

        if(is_array($result)){
            foreach($result as $row){
                fputcsv($outputBuffer,$row);
            }
            fclose($outputBuffer);
        }

        return $outputBuffer;
    }

}