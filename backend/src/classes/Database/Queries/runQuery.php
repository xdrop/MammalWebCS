<?php
require('../../../core.php');

//$query = $_GET["query"];
//$query = json_decode($query, true);
//$myQuery = new FilterQuery();
//
//$results = $myQuery->with(["species1" => 20, "species2" => 22])->fetch();
//
//print_r($results);

$newQueryTest = new FilterQuery();

$results = $newQueryTest->with(["no_of_species"=> 2])->page(1)->limit(5)->fetch();


$speciesCountsQuery = new SpeciesCountsQuery();
$res = $speciesCountsQuery->fetch();

//$newNameQuery = new HabitatNameQuery();

//$results = $newNameQuery->fetch();
//$results = $newQueryTest->with([""])->fetchCSV();
//currently just print for test purposes
//print_r($results);
echo $results->asJSON();
echo $res->asJSON();