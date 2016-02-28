<?php

include('../../core.php');

header('Content-Type: application/json');

if (isset($_REQUEST['action'])) {
    $apiParam = $_REQUEST['action'];
    try {
        if ($apiParam === 'get') {
            $settings = SettingsStorage::settings();
            echo json_encode($settings);
        } else if ($apiParam === 'store') {
            if (isset($_POST['settings'])) {
                $settingsChange = json_decode($_POST['settings'], true);
                if (is_null($settingsChange)) {
                    error('Invalid settings provided, couldn\'t parse the JSON');
                } else {
                    SettingsStorage::set($settingsChange);
                    echo json_encode(["success" => true]);
                }
            } else {
                error('You need to set the post field "settings" before storing data');
            }
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