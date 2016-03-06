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
        echo ["csv" => createCSVFile($queryResults->asCSV()),"results" => $queryResults->asArray()];
    } catch (PDOException $e) {
        error("Failure in database connection.");
    }

} else if (isset($_GET['csv'])){

}
else {
    error("No params provided");
}

function error($status)
{
    echo json_encode(["error" => $status]);
}

function createCSVFile($csvStream){
    $csvPath = SettingsStorage::settings()["csv_file_locations"];
    $rand = Utils::generateRandomString(10) . '.csv';
    FileStorage::storeFile($rand,$csvPath,$csvStream);
    return $rand;
}

function outputCSV($fileStream)
{
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=output.csv');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fileStream));
    readfile($fileStream);
    exit;
}