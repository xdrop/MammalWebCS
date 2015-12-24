<?php


class ClassificationQuery extends Query
{
    const CLASSIFICATION_TABLES_NAME = 'animal';

    const IMAGE_ID_FIELD_NAME = 'photo_id';
    const USER_ID_FIELD_NAME = 'person_id';
    const SPECIES_FIELD_NAME = 'species';
    const NUMBER_OF_ANIMALS_IN_PIC_FIELD = 'number';

    protected function fetchQuery(&$params)
    {
        if(!Utils::keysExist(['imageId'],$params)){
            throw new BadMethodCallException("You need to provide a value to imageId before using this method.");
        }
        $this->internalFetchQuery = $this->db->from(self::CLASSIFICATION_TABLES_NAME)
            ->where(self::CLASSIFICATION_TABLES_NAME.'.' . self::IMAGE_ID_FIELD_NAME . ' = ?',$params['imageId'])
            ->orderBy(self::USER_ID_FIELD_NAME.  ' DESC');
    }


    protected function storeQuery(&$params)
    {
        if(!Utils::keysExist(['imageId','classification'],$params)){
            throw new BadMethodCallException("You need to provide a value to imageId and the" .
                " classification result before using this method.");
        }


        $this->internalStoreQuery = $this->db;

    }

    protected function updateQuery(&$params)
    {
        // TODO: Implement updateQuery() method.
    }

    protected function deleteQuery(&$params)
    {
        // TODO: Implement deleteQuery() method.
    }

    /**
     * Formats the results of this query into a format required by the class using it's results
     * @param $results - The raw results of the query
     * @return mixed
     */
    protected function reformat(&$results)
    {
        $formatted = [];
        foreach($results as $entry){
            $currentUser = $entry[self::USER_ID_FIELD_NAME];
            $currentSpecies = $entry[self::SPECIES_FIELD_NAME];
            $currentNumberOf = $entry[self::NUMBER_OF_ANIMALS_IN_PIC_FIELD];

            if(isset($formatted[$currentUser])){
                $specificUserClassification = &$formatted[$currentUser];
                if(isset($specificUserClassification[$currentSpecies])){
                    $specificUserClassification[$currentSpecies] += $currentNumberOf;
                } else{
                    $specificUserClassification[$currentSpecies] = $currentNumberOf;
                }
            } else{
                $formatted[$currentUser][$currentSpecies] = $currentNumberOf;
            }
        }
        print_r($formatted);
    }
}