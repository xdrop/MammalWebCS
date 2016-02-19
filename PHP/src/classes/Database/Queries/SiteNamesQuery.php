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

    protected function reformat($results)
    {
        if (!is_null($results)) {
            $map = [];
            foreach ($results as $entry) {
                $map[$entry[self::SITE_ID]] = $entry[self::SITE_NAME];
            }
            return $map;

        } else {
            return [];
        }
    }
}