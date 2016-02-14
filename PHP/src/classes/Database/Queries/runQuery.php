<?php
	require('../../../core.php');

	$myQuery = new FilterQuery();
	
	$results = $myQuery->with(["species1" => 20, "species2" => 22])->fetch(); 

	//currently just print for test purposes
	print_r($results);
?>