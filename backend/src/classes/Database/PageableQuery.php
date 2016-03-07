<?php

/**
 * A type of query that can be accessed via pages
 * Class PageableQuery
 */
abstract class PageableQuery extends Query
{
    /**
     * Sets a limit of results
     * @param int $limit The number of results to return
     * @return $this
     */
    public function limit($limit = 20){
        $this->params["limit"] = $limit;
        return $this;
    }

    /**
     * Sets the page
     * @param int $page The page
     * @return $this
     */
    public function page($page){
        $this->params["page"] = $page;
        return $this;
    }

    /**
     * Fetches the results
     * @return null|QueryResults|string
     */
    public function fetch()
    {
        $this->fetchQuery($this->params);

        if (!is_null($this->internalFetchQuery)) {
            if(isset($this->params["limit"])){
                /* get the limit of results */
                $limit = $this->params["limit"];
                /* if they are valid */
                if($limit > 0){
                    if(!isset($this->params["page"]) || !($this->params["page"] > 0)){
                        $page = 1;
                    } else{
                        $page = $this->params["page"];
                    }
                    /* limit the actual query */
                    $this->internalFetchQuery->limit($limit)->offset($limit * ($page - 1));
                }
            }
            return parent::runFetch();
        } else{
            return null;
        }
    }


}