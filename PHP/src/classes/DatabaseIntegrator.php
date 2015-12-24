<?php


class DatabaseIntegrator
{
    private $query;

    private $imageId;

    const CLASSIFICATION_TABLES_NAME = 'animal';

    const IMAGE_ID_FIELD_NAME = 'photo_id';
    const USER_ID_FIELD_NAME = 'person_id';

    public function __construct()
    {

    }
    public function onImage($imageId){
        $this->imageId = $imageId;
        return $this;
    }

    public function fetch(){
        $query = $this->query->from(self::CLASSIFICATION_TABLES_NAME)
            ->where(self::CLASSIFICATION_TABLES_NAME.'.' . self::IMAGE_ID_FIELD_NAME . ' = ?',$this->imageId)
            ->orderBy(self::USER_ID_FIELD_NAME.  ' DESC')->fetchAll();
        print_r($query);
    }

}