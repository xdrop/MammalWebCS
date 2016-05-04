<?php
require('../../../core.php');

$query = new ChartStatsQuery();

print_r($query->with([])->fetch()->asArray());