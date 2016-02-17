<?php

include('../../core.php');

header('Content-Type: application/json');

if(isset($_POST['params'])){
    try{
        $params = json_decode($_POST['params'],true);
        $listNamesQuery = new SpeciesFilterQuery();
        echo $listNamesQuery->with($params)->fetchJSON();
    } catch (PDOException $e){
        error("Failure in database connection.");
    }
} else{
    error("No params provided");
}

function error($status){
    echo json_encode(["error" => $status]);
}