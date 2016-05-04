<?php
require('../../../core.php');

$query = new FilterQuery();

$query->with(['no_of_species' => 2])->fetch()->asArray();


