<?php

include('../../core.php');

header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");


if (isset($_POST['params'])) {
    try {
        $params = json_decode($_POST['params'], true);
        if (is_null($params)) {
            error("Invalid JSON input");
            return;
        }
        $lastId = null;
        /* store as recent query */
        if(Utils::getValue($params["query"],false) === true){
            $recentQueries = new RecentQueries();
            $lastId = $recentQueries->with(["json" => $_POST['params']])->store();
        }
        
        $filterQuery = new FilterQuery();
        $queryResults = $filterQuery->with($params)->fetch();
        echo json_encode(["id" => $lastId,"results" => $queryResults->asArray()],JSON_PRETTY_PRINT);
    } catch (PDOException $e) {
        error("Failure in database connection.");
    }

} else if (isset($_GET['csv'])){
    $csvPath = SettingsStorage::settings()["csv_file_locations"];
    $filename = Utils::sanitizeFilename($_GET['csv']);
    FileStorage::downloadFile($filename, $csvPath);
}
else {
    error("No params provided");
}

function error($status)
{
    echo json_encode(["error" => $status]);
}

/***
 * @param $results QueryResults
 * @return string
 */
function createCSVFile($results){
    $csvPath = SettingsStorage::settings()["csv_file_locations"];
    $rand = Utils::generateRandomString(10) . '.csv';
    $results->asCSV(FileStorage::getPath($rand,$csvPath));
    return $rand;
}

