<?php
	class FilterQuery extends Query
	{
		protected function fetchQuery(&$params){
			/* Query
				SELECT (id, photo_id) FROM classified
					WHERE 'species' = 20 OR 'species' = 22 (The parameter provided)
			 */

			$query = $this->db->from('classified')
						 ->select('id') 
						 ->select('photo_id') 
						 ->where("species = " . $params["species1"] . " OR " . "species = " . $params["species2"]); // assuming whoever calls this passes a species1 and species2 in the params array

			/* Add the query */
			$this->addFetchQuery($query);

			/* From now on whenever someone calls fetch() on the query object,
			   the query you just added will be run. */
		}

		protected function storeQuery(&$params){
			// TODO: Implement storeQuery() method
		}

		protected function updateQuery(&$params)
		{
			// TODO: Implement updateQuery() method.
		}

		protected function deleteQuery(&$params)
		{
			// TODO: Implement deleteQuery() method.
		}
	}
?>