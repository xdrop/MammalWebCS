<?php
require('../../../core.php');

$myQuery = new FilterQuery();

$results = $myQuery->with(["species1" => 20, "species2" => 22])->fetch();

print_r($results);

$newQueryTest = new SpeciesFilterQuery();

$results = $newQueryTest->with(["include" => [20,21], "exclude" => [87]])->fetch();
//currently just print for test purposes
print_r($results);
