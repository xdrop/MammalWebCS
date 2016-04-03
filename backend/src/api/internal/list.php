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
            echo $query->with(["nonempty"=> true])->fetch()->asJSON();
        } else if ($apiParam === 'habitats') {
            $query = new HabitatNameQuery();
            echo $query->with(["nonempty"=> true])->fetch()->asJSON();
        } else if ($apiParam === 'sites') {
            $query = new SiteNameQuery();
            echo $query->with(["nonempty"=> true])->fetch()->asJSON();
        } else if ($apiParam === "all"){
            $species = new SpeciesNameQuery();
            $habitats = new HabitatNameQuery();
            $sites = new SiteNameQuery();
            echo json_encode(["species" => $species->fetch()->asArray(),
                "habitats" => $habitats->fetch()->asArray(),
                "sites" => $sites->fetch()->asArray()]);
        } else if($apiParam === "counts"){
            $counts = new SpeciesCountsQuery();
            echo $counts->fetch()->asJSON();
        } else if($apiParam === "queries") {
            $recentQueries = new RecentQueries();
            echo $recentQueries->fetch()->asJSON();
        } else
        {
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