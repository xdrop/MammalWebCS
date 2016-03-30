<?php


class SpeciesNameQuery extends Query
{

    const OPTIONS_TABLE_NAME = 'options';

    const OPTION_NAME = 'option_name';

    const OPTION_ID = 'option_id';


    protected function fetchQuery(&$params)
    {
        $onlyNonEmpty = Utils::getValue($params["nonempty"],false);

        /* Query
            SELECT (options_name,options_id) FROM options
         */

        $query = $this->db->from(self::OPTIONS_TABLE_NAME)
            ->select([self::OPTION_NAME, self::OPTION_ID])
            ->where('struc', ['mammal', 'bird', 'noanimal']);

        if($onlyNonEmpty){
            $query->select("(SELECT COUNT(*) from classified
            WHERE classified.species = options.option_id) AS counted")
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
                $newEntry["id"] = $entry[self::OPTION_ID];
                $newEntry["name"] = $entry[self::OPTION_NAME];
                $map[] = $newEntry;
            }
            return $map;

        } else {
            return [];
        }
    }
}