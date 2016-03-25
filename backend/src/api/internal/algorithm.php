<?php

include('../../core.php');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");


if (isset($_GET["action"])) {
    $action = $_GET["action"];
    $controller = new AlgorithmController();

    if ($action === "query") {
        header('Content-Type: application/json');
        if (isset($_GET["image_id"])) {
            $imageId = $_GET["image_id"];
            if (isset($_GET["store"]) && $_GET["store"] === true) {
                echo json_encode(["result" => $controller->runOnImage($imageId, true)]);
            } else {
                echo json_encode(["result" => $controller->runOnImage($imageId, false)]);
            }
        } else {
            error("No image id specified.");
        }
    } else if ($action === "empty") {
        header('Content-Type: application/json');
        $controller->clearResults();
        return json_encode(["success" => true]);
    } else if ($action === "run") {
        $controller->runAlgorithm();
    } else {
        header('Content-Type: application/json');
        error("Invalid action");
    }

} else{
    error("No action specified.");
}


function error($status)
{
    echo json_encode(["error" => $status]);
}