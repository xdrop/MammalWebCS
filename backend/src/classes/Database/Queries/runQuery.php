<?php
require('../../../core.php');

$query = new BatchClassificationQuery();

$controller = new AlgorithmController();

$controller->runAlgorithmJobBatch(new JobStatusQuery(),null,false,1,-1);