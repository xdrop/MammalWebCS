<?php


require('algorithm.php');
require('Classification.php');

$mam = new MammalClassifier();
$temp = [['a', 1],['b', 2],['a', 2]];


$res = $mam->on('imageidexample')->classify()->getResult();


print_r($res);
