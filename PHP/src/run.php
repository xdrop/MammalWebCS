<?php

require('includes.php');

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

$res = $classifier->on('imageidexample')->classify()->getResult();


print_r($res);
