<?php


require('algorithm.php');

$mam = new MammalClassifier();
$temp = [['a', 1],['b', 2],['a', 2]];


$mam->on('imageidexample')->classify()->getResult();


print_r($mam->getSpeciesCounts($temp));
