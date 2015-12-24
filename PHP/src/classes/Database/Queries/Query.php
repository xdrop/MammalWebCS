<?php


abstract class Query
{
    protected $db;
    protected $internalFetchQuery;
    protected $internalStoreQuery;
    protected $internalUpdateQuery;
    protected $internalDeleteQuery;
    protected $params;

    public function __construct()
    {
        $this->db = DatabaseConnector::getDatabase();
    }

    public function with($params){
        $this->params = $params;
        return $this;
    }


    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function fetchQuery(&$params);

    /**
     * Creates the SQL query to store the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function storeQuery(&$params);

    /**
     * Creates the SQL query to update the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function updateQuery(&$params);

    /**
     * Creates the SQL query to delete the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected abstract function deleteQuery(&$params);

    /**
     * Formats the results of this query into a format required by the class using it's results
     * @param $results - The raw results of the query
     * @return mixed
     */
    protected abstract function reformat(&$results);


    public function fetch(){
        $this->fetchQuery($this->params);
        return $this->reformat($this->internalFetchQuery->fetchAll());
    }

    public function store(){
        $this->storeQuery($this->params);
        $this->internalStoreQuery->execute();
    }

    public function update(){
        $this->updateQuery($this->params);
        $this->internalUpdateQuery->execute();
    }

    public function delete(){
        $this->deleteQuery($this->params);
        $this->internalDeleteQuery->execute();
    }

}