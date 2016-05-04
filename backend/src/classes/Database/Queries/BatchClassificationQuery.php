<?php


class BatchClassificationQuery extends Query
{

    const CLASSIFICATION_TABLE_NAME = 'animal';
    const CLASSIFIED_TABLE_NAME = 'classified';
    const CLASSIFIED_SCIENTIST_TABLE_NAME = 'classified_scientist';
    const EVENNESS_TABLE_NAME = 'evenness';
    const EVENNESS_SCIENTIST_TABLE_NAME = 'evenness_scientist';

    const IMAGE_ID_FIELD_NAME = 'photo_id';
    const USER_ID_FIELD_NAME = 'person_id';
    const SPECIES_FIELD_NAME = 'species';
    const TIMESTAMP_FIELD_NAME = 'timestamp';
    const NUMBER_OF_ANIMALS_IN_PIC_FIELD = 'number';

    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected function fetchQuery(&$params)
    {
        if (!Utils::keysExist(['from'], $params) || !Utils::keysExist(['to'], $params)) {
            throw new BadMethodCallException("You need to provide a value to imageId before using this method.");
        }

        $fromId = $params['from'];
        $toId = $params['to'];

        /*
          Query equivalent to:
            SELECT DISTINCT animal.person_id, animal.species, animal.number
            FROM animal
            WHERE animal.photo_id = ?
            ORDER BY timestamp ASC
         */

        $query = $this->db
            ->from(self::CLASSIFICATION_TABLE_NAME)
            ->selectDistinct(['photo_id', self::USER_ID_FIELD_NAME,
                self::SPECIES_FIELD_NAME,
                self::NUMBER_OF_ANIMALS_IN_PIC_FIELD])
            ->where(self::IMAGE_ID_FIELD_NAME . ' BETWEEN ? AND ?', $fromId, $toId)
            ->orderBy(self::TIMESTAMP_FIELD_NAME . ' ASC');

        $this->addFetchQuery($query);
    }

    protected function reformat($results)
    {

        $map = [];
        foreach ($results as $entry){
            $currentUser = $entry[self::USER_ID_FIELD_NAME];
            $currentSpecies = $entry[self::SPECIES_FIELD_NAME];
            $currentNumberOf = $entry[self::NUMBER_OF_ANIMALS_IN_PIC_FIELD];
            $currentPhotoId = $entry['photo_id'];


            if(!isset($map[$currentPhotoId])){
                $map[$currentPhotoId] = [];
            }

            $photo = &$map[$currentPhotoId];


            $photo[] = [$currentSpecies => $currentNumberOf];
            
        }

        return $map;



    }


}