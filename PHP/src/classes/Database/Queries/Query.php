<?php


abstract class Query
{
    protected $db;
    protected $internalFetchQueries;
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
        $this->internalFetchQueries = null;
        $this->internalStoreQueries = null;
        $this->internalUpdateQueries = null;
        $this->internalDeleteQueries = null;
        return $this;
    }


    /**
     * Creates the SQL queries to fetch the data required and stores it in the appropriate internal query
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
    protected abstract function reformat($results);


    public function fetch()
    {
        $this->fetchQuery($this->params);
        $queries = $this->internalFetchQueries;
        if (!is_null($this->internalFetchQueries)) {
            return $this->reformat($this->internalFetchQueries->fetchAll());
        }
    }

    public function store()
    {
        $this->storeQuery($this->params);
        $queries = $this->internalStoreQueries;
        if (!is_null($queries)) {
            if(is_array($queries)){
                foreach($this->internalStoreQueries as $query){
                    $query->execute();
                }
            } else{
                $queries.execute();
            }

        }
    }

    public function update()
    {
        $this->updateQuery($this->params);
        $queries = $this->internalUpdateQueries;
        if (!is_null($queries)) {
            if(is_array($queries)){
                foreach($this->internalUpdateQueries as $query){
                    $query->execute();
                }
            } else{
                $queries.execute();
            }

        }
    }

    public function delete()
    {
        $this->deleteQuery($this->params);
        $queries = $this->internalDeleteQueries;
        if (!is_null($queries)) {
            if(is_array($queries)){
                foreach($this->internalDeleteQueries as $query){
                    $query->execute();
                }
            } else{
                $queries.execute();
            }
            
        }
    }

}