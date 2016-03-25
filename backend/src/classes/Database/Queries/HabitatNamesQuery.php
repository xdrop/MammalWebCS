<?php


class HabitatNameQuery extends Query
{

    const HABITAT_TABLE_NAME = 'options';

    const HABITAT_NAME = 'option_name';

    const HABITAT_ID = 'option_id';


    protected function fetchQuery(&$params)
    {

        /* Query
            SELECT (site_name, site_id) FROM site
         */

        $query = $this->db->from(self::HABITAT_TABLE_NAME)
            ->select([self::HABITAT_NAME, self::HABITAT_ID])
			->where('struc', 'habitat');

        /* Add the query */
        $this->addFetchQuery($query);

    }


    protected function reformat($results)
    {
        if (!is_null($results)) {
            $map = [];
            foreach ($results as $entry) {
                $newEntry = [];
                $newEntry["id"] = $entry[self::HABITAT_ID];
                $newEntry["name"] = $entry[self::HABITAT_NAME];
                $map[] = $newEntry;
            }
            return $map;

        } else {
            return [];
        }

    }
}