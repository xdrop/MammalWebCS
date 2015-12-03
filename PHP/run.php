<?php


require('algorithm.php');

$mam = new MammalClassifier();
$temp = [['a', 1],['b', 2],['a', 2]];


print_r($mam->getSpeciesCounts($temp));
