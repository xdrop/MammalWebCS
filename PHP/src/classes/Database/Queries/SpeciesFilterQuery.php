<?php


class SpeciesFilterQuery extends Query
{

    const CLASSIFICATION_RESULTS_TABLE_NAME = 'classified';

    protected function fetchQuery(&$params)
    {

        /* Check if at least the include or exclude param is passed */

        if (!Utils::keysExist("include", $params) && !Utils::keysExist("exclude", $params)) {
            throw new BadMethodCallException("You need to provide species to include/exclude before using this method.");
        }

        $speciesToInclude = Utils::getValue($params["include"], []);
        $speciesToExclude = Utils::getValue($params["exclude"], []);

        $hasSpeciesToInclude = count($speciesToInclude) > 0;
        $hasSpeciesToExclude = count($speciesToExclude) > 0;

        // this generates the appropriate number of unbound variables for use in IN (?,?,?...)
        // this unbound variables have to be passed values to through the arguments of the where method
        $inQuery = $hasSpeciesToInclude ? implode(',', array_fill(0, count($speciesToInclude), '?')) : '';
        $notInQuery = $hasSpeciesToExclude ? implode(',', array_fill(0, count($speciesToExclude), '?')) : '';


        // SELECT * FROM classified
        // WHERE species IN (?,?,?,...) AND NOT IN (?,?,...)

        $query = $this->db->from('classified')
            ->select('*');

        if ($hasSpeciesToInclude) {
            $query->where("species IN ($inQuery)", ['expand' => $speciesToInclude]);
        }

        if ($hasSpeciesToExclude) {
            $query->where("species NOT IN ($notInQuery)", ["expand" => $speciesToExclude]);
        }

        /* expand is a special keyword which says take the arguments from the list and bind them to unbound variables
           eg. Query is WHERE NOT IN (?,?)
                $args = [15,22]
                passing in ['expand' => $args]
                replaces the ?,? with 15,22 as in WHERE NOT IN (15,22)
        */

        $this->addFetchQuery($query);

    }


    protected function storeQuery(&$params)
    {

    }

    protected function updateQuery(&$params)
    {

    }

    protected function deleteQuery(&$params)
    {

    }


}