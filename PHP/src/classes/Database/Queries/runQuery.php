<?php
require('../../../core.php');

$query = $_GET["query"];
//$query = json_decode($query, true);
//$myQuery = new FilterQuery();
//
//$results = $myQuery->with(["species1" => 20, "species2" => 22])->fetch();
//
//print_r($results);

$newQueryTest = new SpeciesFilterQuery();

$results = $newQueryTest->with(["species_include" => $query])->fetch();

//$newNameQuery = new HabitatNameQuery();

//$results = $newNameQuery->fetch();
//$results = $newQueryTest->with([""])->fetchCSV();
//currently just print for test purposes
//print_r($results);
echo json_encode(array("filterResults" => $results));

?>