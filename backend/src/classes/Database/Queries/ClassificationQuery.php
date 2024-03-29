<?php


class ClassificationQuery extends Query
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

    protected function fetchQuery(&$params)
    {
        if (!Utils::keysExist(['imageId'], $params)) {
            throw new BadMethodCallException("You need to provide a value to imageId before using this method.");
        }


        $imageId = $params['imageId'];

        /*
          Query equivalent to:
            SELECT DISTINCT animal.person_id, animal.species, animal.number
            FROM animal
            WHERE animal.photo_id = ?
            ORDER BY timestamp ASC
         */

        $query = $this->db
            ->from(self::CLASSIFICATION_TABLE_NAME)
            ->selectDistinct([self::USER_ID_FIELD_NAME,
                self::SPECIES_FIELD_NAME,
                self::NUMBER_OF_ANIMALS_IN_PIC_FIELD])
            ->where(self::CLASSIFICATION_TABLE_NAME . '.' .
                self::IMAGE_ID_FIELD_NAME . ' = ?', $imageId)
            ->orderBy(self::TIMESTAMP_FIELD_NAME . ' ASC');

        $this->addFetchQuery($query);
    }


    protected function storeQuery(&$params)
    {
        if (!Utils::keysExist(['imageId', 'result'], $params)) {
            throw new BadMethodCallException("You need to provide a value to imageId and the" .
                " query result before using this method.");
        }

        $result = $params['result'];

        $values = [];

        if ($result != MammalClassifier::NOT_ENOUGH_TO_CLASSIFY) {
            if ($result['classification'] != MammalClassifier::FLAGGED_FOR_SCIENTIST) {
                foreach ($result['classification'] as $species => $numberOf) {
                    $values[] = [
                        'id' => null,
                        'photo_id' => $params['imageId'],
                        'species' => $species,
                        'count' => $numberOf,
                        'flagged' => false
                    ];
                }
            } else {
                $values = [
                    'id' => null,
                    'photo_id' => $params['imageId'],
                    'species' => '0',
                    'count' => '0',
                    'flagged' => true
                ];
            }
            $evenness = [
                'id' => null,
                'photo_id' => $params['imageId'],
                'evenness_species' => $params['result']['evenness_species'],
                'evenness_count' => $params['result']['evenness_count']
            ];

            /* depending on whether we are storing/updating temporary scientist results
               or results in the public table we switch the table name accordingly */
            $classifiedTableName
                = Utils::getValue($params["scientist_dataset"],false) ? self::CLASSIFIED_SCIENTIST_TABLE_NAME
                                                              : self::CLASSIFIED_TABLE_NAME;
            $evennessTableName
                = Utils::getValue($params["scientist_dataset"],false) ? self::EVENNESS_SCIENTIST_TABLE_NAME
                                                              : self::EVENNESS_TABLE_NAME;

            /* Query
                INSERT INTO classified (id, photo_id, species, count, flagged)
                VALUES (NULL, 221, 22, '1', 0)
            */

            $this->addStoreQuery($this->db->insertInto($classifiedTableName)->values($values));

            /* Query
                INSERT INTO evenness (id, photo_id, evenness_species, evenness_count)
                VALUES (...)
             */

            $this->addStoreQuery($this->db->insertInto($evennessTableName)->values($evenness));
        }

    }

    protected function deleteQuery(&$params)
    {
        if($params["all"]){
            if($params["scientist_dataset"]){
                $this->db->getPdo()->prepare("TRUNCATE " . self::CLASSIFIED_SCIENTIST_TABLE_NAME)->execute();
            }
            $this->db->getPdo()->prepare("TRUNCATE " . self::CLASSIFIED_TABLE_NAME)->execute();
        };
    }


    protected function updateQuery(&$params)
    {
        if (!Utils::keysExist(['imageId', 'result'], $params)) {
            throw new BadMethodCallException("You need to provide a value to imageId and the" .
                " query result before using this method.");
        }

        /* depending on whether we are storing/updating temporary scientist results
           or results in the public table we switch the table name accordingly
         */
        $classifiedTableName
            = Utils::getValue($params["scientist_dataset"],false) ? self::CLASSIFIED_SCIENTIST_TABLE_NAME
            : self::CLASSIFIED_TABLE_NAME;
        $evennessTableName
            = Utils::getValue($params["scientist_dataset"],false) ? self::EVENNESS_SCIENTIST_TABLE_NAME
            : self::EVENNESS_TABLE_NAME;

        /* firstly delete any previous classifications for image */
        $this->db->deleteFrom($classifiedTableName)
            ->where("photo_id",$params["imageId"])->execute();
        $this->db->deleteFrom($evennessTableName)
            ->where("photo_id",$params["imageId"])->execute();

        $this->store();
    }


    /**
     * Formats the results of this query into a format required by the class using it's results
     * @param $results - The raw results of the query
     * @return mixed
     */
    protected function reformat($results)
    {
        $map = [];
        $formatted = [];
        foreach ($results as $entry) {
            $currentUser = $entry[self::USER_ID_FIELD_NAME];
            $currentSpecies = $entry[self::SPECIES_FIELD_NAME];
            $currentNumberOf = $entry[self::NUMBER_OF_ANIMALS_IN_PIC_FIELD];

            if (isset($map[$currentUser])) {
                $specificUserClassification = &$map[$currentUser];
                if (isset($specificUserClassification[$currentSpecies])) {
                    $specificUserClassification[$currentSpecies] += $currentNumberOf;
                } else {
                    $specificUserClassification[$currentSpecies] = $currentNumberOf;
                }
            } else {
                $map[$currentUser][$currentSpecies] = $currentNumberOf;
            }

            $formatted[] = new Classification($map[$currentUser]);
        }

        return $formatted;
    }
}