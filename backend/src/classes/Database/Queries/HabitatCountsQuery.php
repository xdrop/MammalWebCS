<?php


class HabitatCountsQuery extends Query
{

    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected function fetchQuery(&$params)
    {
        $query = $this->db->from("classified")
            ->select(["options.option_name as name","site.site_id","COUNT(classified.photo_id) as count"])
            ->leftJoin("photo on classified.photo_id = photo.photo_id")
            ->leftJoin("site on photo.site_id = site.site_id")
            ->leftJoin("options on site.habitat_id = options.option_id")
            ->groupBy("site.habitat_id");

        $this->addFetchQuery($query);

    }

}