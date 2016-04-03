<?php


class RecentQueries extends Query
{

    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected function fetchQuery(&$params)
    {
        $value = Utils::getValue($params["id"], false);

        if($value){
            $this->addFetchQuery($this->db->from('recent_queries')->select('*')->orderBy("id DESC")->limit(10));
        } else{
            $this->addFetchQuery($this->db->from('recent_queries')->select('*')->where('id',$value));
        }
    }


    protected function storeQuery(&$params)
    {
        $this->addStoreQuery($this->db->insertInto('recent_queries')->values(["json" => $params["json"]]));
    }


}