<?php


class ClassificationResultsQuery extends Query {

	const CLASSIFICATION_RESULTS_TABLE_NAME = 'classifications';
	const EVENNESS_RESULTS_TABLE_NAME = 'evenness';

	// either ALL or OR (include) or NOT (except)
	// either by species
	// flagged
	// presence of humans
	// site id
	// 

	protected function fetchQuery(&$params){
		if(Utils::keysExist([''],$params)){

		} else if(Utils::keysExist([''],$params)){

		} else if (Utils::keysExist([''],$params)){

		}
	}

	protected function storeQuery(&$params){

	}

	protected function updateQuery(&$params){
		
	}

	protected function deleteQuery(&$params){
		
	}

	protected function reformat(&$results){
		
	}

}