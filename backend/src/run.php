<?php

require('core.php');

$classifier = new MammalClassifier();

// Usage:

/* Create a MammalClassifier object
    -> call on() to specify the id of the image
        this will load up the image classifications from the database
    -> call classify to run the classification algorithm
    -> call store() to store result back in database
    -> call getResult() to get the result

    Order is (most of the time) important!
*/

$controller = new AlgorithmController();

$controller->runAlgorithm();


$id = 311;

while($id <= 311){
    $res = $classifier->on($id)->classify()->store()->getResult();
    $id++;
    print("id: ". $id . " =>");
    print_r($res);
    print("\n");
}






