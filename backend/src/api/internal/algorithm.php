<?php

require('../../core.php');

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
            $scientistDataset = isset($_GET["scientist_dataset"]) && $_GET["scientist_dataset"];
            if (isset($_GET["store"]) && $_GET["store"]) {
                echo json_encode(["result" => $controller->runOnImage($imageId, true, null, $scientistDataset)]);
            } else {
                echo json_encode(["result" => $controller->runOnImage($imageId, false, null, $scientistDataset)]);
            }
        } else {
            error("No image id specified.");
        }
    } else if($action === "status"){
        $st = new JobStatusQuery();
        echo $status = $st->fetch()->asJSON();
    }  else {
        error("Invalid action");
    }

} else if (isset($_POST["action"])) {
    $action = $_POST["action"];
    if ($action === "run") {
        $st = new JobStatusQuery();

        $status = $st->fetch()->asArray();
        if($status["started"]){
            error("Algorithm already started.");
            return;
        }
        if (isset($_POST["settings"])) {
            $testValid = json_decode($_POST['settings'], true);
            if (is_null($testValid)) {
                error("Invalid JSON input");
                return;
            }
        }
        $scientistDataset = Utils::getValue($_POST["scientist_dataset"], false);
        $fromID = Utils::getValue($_POST["from_id"], 1);
        $toID = Utils::getValue($_POST["to_id"], -1);

        $url = Environment::siteSettings()["path"];
        $url = $url . "/jobs/run_algorithm_job.php";
        $fields = array(
            'scientist_dataset' => urlencode($scientistDataset),
            'from_id' => urlencode($fromID),
            'to_id' => urlencode($toID),
            'settings' => urlencode(Utils::getValue($_POST["settings"],"invalid"))
        );

        $fields_string = "";

        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        echo json_encode(["success" => true, "log" => "Algorithm started."]);

    } else if ($action === "empty") {
        $scientistDataset = Utils::getValue($_POST["scientist_dataset"],false);
        $controller->clearResults($scientistDataset);
        return json_encode(["success" => true]);
    }
} else {
    error("No action specified.");
}

function error($status)
{
    echo json_encode(["error" => $status]);
}