<?php

include('../../core.php');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");

header('Content-Type: application/json');


if (isset($_GET["action"])) {
    $action = $_GET["action"];
    $controller = new AlgorithmController();

    if ($action === "query") {
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
        $controller->clearResults();
        return json_encode(["success" => true]);
    } else if ($action === "run") {
        header_remove("Content-Type");
        $controller->runAlgorithm();
    } else {
        error("Invalid action");
    }

} else{
    error("No action specified.");
}


function error($status)
{
    echo json_encode(["error" => $status]);
}