<?php


abstract class Query
{
    protected $db;

    /**
     * @var SelectQuery the fetch query
     */
    protected $internalFetchQuery;

    /**
     * @var InsertQuery|InsertQuery[] the insert query
     */
    protected $internalStoreQueries;

    /**
     * @var UpdateQuery|UpdateQuery[] the update query
     */
    protected $internalUpdateQueries;

    /**
     * @var DeleteQuery|DeleteQuery[] the delete query
     */
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
     * @return QueryResults|string|null Fetches the results of the query
     */
    public function fetch()
    {
        $this->fetchQuery($this->params);

        if (!is_null($this->internalFetchQuery)) {
            return $this->runFetch();
        } else{
            return null;
        }
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
                $queries->execute();
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
                $queries->execute();
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
                $queries->execute();
            }

        }
    }

    /**
     * Runs the query
     * @return QueryResults|string
     */
    protected function runFetch()
    {
        $result = $this->reformat($this->internalFetchQuery->fetchAll());
        return !$result ? "none" : new QueryResults($result);
    }

}