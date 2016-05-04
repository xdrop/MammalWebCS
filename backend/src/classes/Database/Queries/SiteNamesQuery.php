<?php


class SiteNameQuery extends Query
{

    const SITE_TABLE_NAME = 'site';

    const SITE_NAME = 'site_name';

    const SITE_ID = 'site_id';


    protected function fetchQuery(&$params)
    {

        $onlyNonEmpty = Utils::getValue($params["nonempty"],false);

        /* Query
            SELECT (site_name, site_id) FROM site
         */

        $query = $this->db->from(self::SITE_TABLE_NAME)
            ->select([self::SITE_NAME, self::SITE_ID]);


        if($onlyNonEmpty){
            $query->select("(SELECT COUNT(*) from classified
            LEFT JOIN photo ON photo.photo_id = classified.photo_id
            WHERE photo.site_id = site.site_id) AS counted")
                ->having("counted > 0");
        }

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