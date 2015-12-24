<?php

require('../../src/core.php');

$classifier = new MammalClassifier();

$testJson = '[{"buffalo":1,"antelope":2},{"buffalo":2},{"cat":2},{"buffalo":1},{"cat":2},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":2},{"giraffe":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":2,"antelope":1},{"buffalo":2,"antelope":1},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"nothing_here":0},{"buffalo":1,"antelope":1},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2}]';

if(isset($_POST['data'])){
    echo '<pre>';
    $classifications = json_decode($_POST['data'],true);
    echo "\nResult: \n";
    print_r($classifier->onDataSet($classifications)->classify()->getResult());
    echo '</pre>';
} else{
    $classifications = json_decode($testJson,true);
    echo "\nResult: \n";
    print_r($classifier->onDataSet($classifications)->classify()->getResult());
}
