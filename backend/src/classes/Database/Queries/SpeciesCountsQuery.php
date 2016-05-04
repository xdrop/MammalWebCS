<?php


class SpeciesCountsQuery extends Query
{

    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected function fetchQuery(&$params)
    {
        $query = $this->db->from("classified")
            ->select(["species","COUNT(species) as count"])
            ->leftJoin("options on options.option_id = classified.species")
            ->select("options.option_name AS name")
            ->groupBy("species");

        $this->addFetchQuery($query);

    }

}