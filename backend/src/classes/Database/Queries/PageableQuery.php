<?php


abstract class PageableQuery extends Query
{
    public function limit($limit = 20){
        $this->params["limit"] = $limit;
        return $this;
    }

    public function page($page){
        $this->params["page"] = $page;
        return $this;
    }

    public function fetch()
    {
        $this->fetchQuery($this->params);

        if (!is_null($this->internalFetchQuery)) {
            $limit = $this->params["limit"];
            $page = $this->params["page"];
            if($limit > 0 && $page > 0){
                $this->internalFetchQuery->limit($limit)->offset($limit * ($page - 1));
            }
            $result = $this->reformat($this->internalFetchQuery->fetchAll());
            return !$result ? "none" : new QueryResults($result);
        } else{
            return null;
        }
    }


}