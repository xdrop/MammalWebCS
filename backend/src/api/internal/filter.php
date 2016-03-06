<?php

include('../../core.php');

header('Content-Type: application/json');

if (isset($_POST['params'])) {
    try {
        $params = json_decode($_POST['params'], true);
        if (is_null($params)) {
            error("Invalid JSON input");
            return;
        }
        $listNamesQuery = new FilterQuery();
        $queryResults = $listNamesQuery->with($params)->fetch();
        echo json_encode(["csv" => createCSVFile($queryResults),"results" => $queryResults->asArray()]);
    } catch (PDOException $e) {
        error("Failure in database connection.");
    }

} else if (isset($_GET['csv'])){
    $csvPath = SettingsStorage::settings()["csv_file_locations"];
    $filename = $_GET['csv'];
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

