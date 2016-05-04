<?php

require "../core.php";

$onScientistData = Utils::getValue($_POST["scientist_dataset"],false);
$fromID = Utils::getValue($_POST["from_id"],1);
$toID = Utils::getValue($_POST["to_id"],-1);
$settings = Utils::getValue($_POST["settings"],null);

if($settings){
    $settingsObj = json_decode($settings,true);
} else{
    $settingsObj = null;
}

$st = new JobStatusQuery();
$status = $st->fetch()->asArray();

$controller = new AlgorithmController();

/* if already running then abort */
if($status["started"]){
    return;
} 
/* ignore user closing the connection */
ignore_user_abort(true);

/* allow infinite time */
set_time_limit(0);


$controller->runAlgorithmJobBatch($st,$settingsObj,$onScientistData,$fromID,$toID);
