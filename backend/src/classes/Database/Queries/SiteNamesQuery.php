<?php


class SiteNameQuery extends Query
{

    const SITE_TABLE_NAME = 'site';

    const SITE_NAME = 'site_name';

    const SITE_ID = 'site_id';


    protected function fetchQuery(&$params)
    {

        /* Query
            SELECT (site_name, site_id) FROM site
         */

        $query = $this->db->from(self::SITE_TABLE_NAME)
            ->select([self::SITE_NAME, self::SITE_ID]);

        /* Add the query */
        $this->addFetchQuery($query);

    }


    protected function reformat($results)
    {
        if (!is_null($results)) {
            $map = [];
            foreach ($results as $entry) {
                $newEntry = [];
                $newEntry["id"] = $entry[self::SITE_ID];
                $newEntry["name"] = $entry[self::SITE_NAME];
                $map[] = $newEntry;
            }
            return $map;

        } else {
            return [];
        }


    }
}