<?php

include('../../core.php');

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

if (isset($_GET['item'])) {
    $apiParam = $_GET['item'];
    try {
        if ($apiParam === 'species') {
            $query = new SpeciesNameQuery();
            echo $query->fetch()->asJSON();
        } else if ($apiParam === 'habitats') {
            $query = new HabitatNameQuery();
            echo $query->fetch()->asJSON();
        } else if ($apiParam === 'sites') {
            $query = new SiteNameQuery();
            echo $query->fetch()->asJSON();
        } else {
            error("Invalid action");
        }
    } catch (PDOException $e) {
        error("Failure in database connection.");
    }
} else {
    error("No action provided");
}

function error($status)
{
    echo json_encode(["error" => $status]);
}