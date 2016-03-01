<?php


abstract class Query
{
    protected $db;
    protected $internalFetchQuery;
    protected $internalStoreQueries;
    protected $internalUpdateQueries;
    protected $internalDeleteQueries;
    protected $params;

    public function __construct()
    {
        $this->db = DatabaseConnector::getDatabase();
    }

    public function with($params)
    {
        $this->params = $params;
        /* Only one fetch query makes sense because otherwise we can't group results */
        $this->internalFetchQuery = null;
        $this->internalStoreQueries = [];
        $this->internalUpdateQueries = [];
        $this->internalDeleteQueries = [];
        return $this;
    }


    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function fetchQuery(&$params);

    /**
     * Creates the SQL queries to store the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function storeQuery(&$params);

    /**
     * Creates the SQL queries to update the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function updateQuery(&$params);

    /**
     * Creates the SQL queries to delete the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function deleteQuery(&$params);

    /**
     * Formats the results of this query into a format required by the class using it's results
     * @param $results - The raw results of the query
     * @return mixed
     */
    protected function reformat($results)
    {
        return $results;
    }


    protected function addFetchQuery(&$query)
    {
        $this->internalFetchQuery = $query;
    }

    protected function addStoreQuery(&$query)
    {
        if (!is_null($query)) {
            $this->internalStoreQueries[] = $query;
        }
    }

    protected function addUpdateQuery(&$query)
    {
        if (!is_null($query)) {
            $this->internalUpdateQueries[] = $query;
        }
    }

    protected function addDeleteQuery(&$query)
    {
        if (!is_null($query)) {
            $this->internalDeleteQueries[] = $query;
        }
    }


    /**
     * @return mixed|null Fetches the results of the query
     */
    public function fetch()
    {
        $this->fetchQuery($this->params);

        if (!is_null($this->internalFetchQuery)) {
            return $this->reformat($this->internalFetchQuery->fetchAll());
        } else{
            return null;
        }
    }

    /**
     * @return string Fetches the results of the query in JSON format
     */
    public function fetchJSON(){
        return json_encode($this->fetch());
    }

    /**
     * @return string Fetches the results of the query in CSV format
     */
    public function fetchCSV(){
        $result = $this->fetch();
        $outputBuffer = fopen("php://output", 'w');

        if(is_array($result)){
            foreach($result as $row){
                fputcsv($outputBuffer,$row);
            }
            fclose($outputBuffer);
        }

        return $outputBuffer;
    }

    public function store()
    {
        $this->storeQuery($this->params);
        $queries = $this->internalStoreQueries;
        if (!is_null($queries)) {
            if (is_array($queries)) {
                foreach ($this->internalStoreQueries as $query) {
                    $query->execute();
                }
            } else {
                $queries . execute();
            }

        }
    }

    public function update()
    {
        $this->updateQuery($this->params);
        $queries = $this->internalUpdateQueries;
        if (!is_null($queries)) {
            if (is_array($queries)) {
                foreach ($this->internalUpdateQueries as $query) {
                    $query->execute();
                }
            } else {
                $queries . execute();
            }

        }
    }

    public function delete()
    {
        $this->deleteQuery($this->params);
        $queries = $this->internalDeleteQueries;
        if (!is_null($queries)) {
            if (is_array($queries)) {
                foreach ($this->internalDeleteQueries as $query) {
                    $query->execute();
                }
            } else {
                $queries . execute();
            }

        }
    }

}