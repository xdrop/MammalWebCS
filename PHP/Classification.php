<?php


class Classification extends ArrayObject
{
    private $data;


    public function __construct(array $data)
    {
        $this->data = $data;
        parent::__construct($this->data);
    }

    public function getNumOfDiffAnimals(){
        return count($this->data);
    }


    public function hash(){
        return implode('|',$this->data);
    }

}