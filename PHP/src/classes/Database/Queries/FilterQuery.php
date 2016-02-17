<?php

class FilterQuery extends Query
{
    protected function fetchQuery(&$params)
    {

        if (!Utils::keysExist(['species1', 'species2'], $params)) {
            throw new BadMethodCallException("You need to provide a value to species1 and species2 before using this query.");
        }

        /* Query
            SELECT (id, photo_id) FROM classified
                WHERE 'species' = 20 OR 'species' = 22 (The parameter provided)
         */

        $query = $this->db->from('classified')
            ->select('id')
            ->select('photo_id')
            ->where("species = ? OR species = ?", $params["species1"], $params["species2"]); // assuming whoever calls this passes a species1 and species2 in the params array

        /* Add the query */
        $this->addFetchQuery($query);

        /* From now on whenever someone calls fetch() on the query object,
           the query you just added will be run. */
    }

    protected function storeQuery(&$params)
    {
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