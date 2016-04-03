<?php


class JobStatusQuery extends Query
{

    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected function fetchQuery(&$params)
    {
        $query = $this->db->from("jobs")->select("*")->where("type","algorithm")->limit(1);
        $this->addFetchQuery($query);
    }


    protected function updateQuery(&$params)
    {
        $query = $this->db->update("jobs")->set($params)->where("type","algorithm");
        $this->addUpdateQuery($query);
    }

    protected function reformat($results){
        $results = $results[0];
        unset($results["id"]);
        if($results["started"]){
            $results["started"] = true;
        } else{
            $results["started"] = false;
        }
        return $results;
    }


}