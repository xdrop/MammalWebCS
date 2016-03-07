<?php


class FilterQuery extends PageableQuery
{

    const CLASSIFICATION_RESULTS_TABLE_NAME = 'classified';

    protected function fetchQuery(&$params)
    {
        $usersToInclude = Utils::getValue($params["users_include"], []);
        $usersToExclude = Utils::getValue($params["users_exclude"], []);

        $speciesToInclude = Utils::getValue($params["species_include"], []);
        $speciesToExclude = Utils::getValue($params["species_exclude"], []);

        $hasSpeciesToInclude = count($speciesToInclude) > 0;
        $hasSpeciesToExclude = count($speciesToExclude) > 0;
        $hasUsersToInclude = count($usersToInclude) > 0;
        $hasUsersToExclude = count($usersToExclude) > 0;

        // date has to be YYYY-MM-DD HH:MM:SS
        $hasTimeStamps = Utils::keysExist(["taken_start", "taken_end"], $params);

        $hasFlagged = Utils::keysExist("flagged", $params);

        $hasSiteId = Utils::keysExist("site_id", $params);

        $hasHumans = Utils::keysExist("contains_human", $params);

        $hasNumberOfClassifications = Utils::keysExist("no_of_classifications", $params);

        $hasHabitatType = Utils::keysExist("habitat_id",$params);

        $hasNumberOfClassificationsFrom = Utils::keysExist("no_of_classifications_from",$params);

        $hasNumberOfClassificationsTo = Utils::keysExist("no_of_classifications_to",$params);



        // SELECT * FROM classified
        // WHERE species IN (?,?,?,...) AND NOT IN (?,?,...)

        $query = $this->db->from('classified')
            ->select(['classified.photo_id', 'classified.species', 'classified.flagged',
                'classified.timestamp AS time_classified'])
            ->leftJoin('photo ON photo.photo_id = classified.photo_id')
            ->select(['photo.taken', 'photo.person_id', 'photo.site_id',
                'photo.filename', 'photo.contains_human'])
            ->leftJoin('site ON site.site_id = photo.site_id')
            ->select(['site.habitat_id','site.site_name'])
            ->leftJoin('options ON options.option_id = classified.species')
            ->select('options.option_name AS species_name')
            ->leftJoin('evenness ON evenness.photo_id = classified.photo_id')
            ->select(['evenness.evenness_species','evenness.evenness_count'])
            ->leftJoin('options AS options2 ON options2.option_id = site.habitat_id')
            ->select('options2.option_name AS habitat_name');

        if($hasNumberOfClassificationsFrom && $hasNumberOfClassificationsTo){
            $classificationsFrom = $params["no_of_classifications_from"];
            $classificationsTo = $params["no_of_classifications_to"];
            $query->leftJoin('animal ON animal.photo_id = classified.photo_id')
                ->select('COUNT(DISTINCT animal.person_id) AS no_of_classifications')
                ->groupBy('photo_id')
                ->having("COUNT(DISTINCT animal.person_id) BETWEEN ? AND ?",$classificationsFrom, $classificationsTo);
        } else if($hasNumberOfClassifications){
            $numberOfClassifications = $params["no_of_classifications"];
            $query->leftJoin('animal ON animal.photo_id = classified.photo_id')
                ->select('COUNT(DISTINCT animal.person_id) AS no_of_classifications')
                ->groupBy('photo_id')
                ->having("COUNT(DISTINCT animal.person_id) = ?",$numberOfClassifications);
        }

        if($hasHabitatType){
            $habitatType=  $params["habitat_id"];
            $query->where('site.habitat_id',$habitatType);
        }

        if ($hasSpeciesToInclude) {
            $query->where("species", $speciesToInclude);
        }

        if ($hasSpeciesToExclude) {
            $unknowns = Utils::generateUnknowns($speciesToExclude);
            $query->where("species NOT IN ($unknowns)", ["expand" => $speciesToExclude]);
        }

        if($hasUsersToInclude){
            $query->where("photo.person_id", $usersToInclude);
        }

        if($hasUsersToExclude){
            $unknowns = Utils::generateUnknowns($usersToExclude);
            $query->where("photo.person_id NOT IN ($unknowns)", ['expand' => $usersToExclude]);
        }

        if ($hasTimeStamps) {
            $takenStart = $params['taken_start'];
            $takenEnd = $params['taken_end'];
            $query->where("photo.taken BETWEEN ? AND ?", $takenStart, $takenEnd);
        }

        if ($hasFlagged) {
            $flagged = $params['flagged'];
            $query->where("classified.flagged", $flagged);
        }

        if ($hasSiteId) {
            $siteId = $params['site_id'];
            $query->where("photo.site_id", $siteId);
        }

        if($hasHumans){
            $humans = $params['contains_human'];
            $query->where("photo.contains_human", $humans);
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

    protected function reformat($results)
    {
        if($results){
            foreach($results as &$element){

                $person_id = $element["person_id"];
                $site_id = $element["site_id"];
                $filename = $element["filename"];
                $element['url'] = ImageURL::getURL($person_id,$site_id,$filename);
                unset($element['filename']);
            }
        }

        return $results;
    }


}