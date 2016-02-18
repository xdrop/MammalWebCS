<?php
require('../../../core.php');

//$myQuery = new FilterQuery();
//
//$results = $myQuery->with(["species1" => 20, "species2" => 22])->fetch();
//
//print_r($results);

$newQueryTest = new SpeciesFilterQuery();

$results = $newQueryTest->with(["no_of_classifications" => 10])->fetchCSV();
//currently just print for test purposes
print($results);
