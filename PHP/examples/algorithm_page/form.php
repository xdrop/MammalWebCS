<?php

require('../../src/includes.php');

$classifier = new MammalClassifier();


if(isset($_POST['data'])){
    $classifications = json_decode($_POST['data'],true);
    print_r($classifier->onDataSet($classifications)->classify()->getResult());
}
