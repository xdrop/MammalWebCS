<?php

require('../../core.php');


$csv = Utils::getValue($_GET['id']);

if($csv){
    $q = new RecentQueries();
    try{
        $json = $q->with(["id" => $csv])->fetch();
        if($json instanceof QueryResults){
            $json->asArray()[0]["json"];
        } else{
            throw new Exception();
        }
        $params = json_decode($json,true);
        if(isset($params["page"])){
            unset($params["page"]);
        }
        if(isset($params["limit"])){
            unset($params["limit"]);
        }
        $filterQuery = new FilterQuery();
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=output.csv");
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
        header("Pragma: no-cache"); // HTTP 1.0
        header("Expires: 0"); // Proxies
        $queryResults = $filterQuery->with($params)->fetch()->asCSV();
    } catch (Exception $e){
        error("Invalid id");
        return;
    }

} else{
    error("No id provided");
}

function error($status)
{
    header('Content-Type: application/json');

    header('Access-Control-Allow-Origin: *');

    header('Access-Control-Allow-Methods: GET, POST');

    header("Access-Control-Allow-Headers: X-Requested-With");
    
    echo json_encode(["error" => $status]);
}