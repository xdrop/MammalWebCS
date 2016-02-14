<?php

include('../../core.php');

header('Content-Type: application/json');

if(isset($_GET['action'])){
    if($_GET['action'] === 'list') {
        try{
            $listNamesQuery = new SpeciesNameQuery();
            echo json_encode($listNamesQuery->fetch());
        } catch (PDOException $e){
            error("Failure in database connection.");
        }
    } else{
        error("Invalid action");
    }
} else{
    error("No action provided");
}

function error($status){
    echo json_encode(["error" => $status]);
}