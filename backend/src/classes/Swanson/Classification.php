<?php


class Classification extends ArrayObject
{
    private $data;


    public function __construct(array $data)
    {
        $this->data = &$data;
        parent::__construct($this->data);
    }


    public function sum(){
        return array_sum($this->data);
    }

    public function remove($name){
        $this->offsetUnset($name);
        unset($this->data[$name]);
    }


    public function hashed(){
        ksort($this->data);
        return http_build_query($this->data,'','|');
    }

    public function toArray(){
        return $this->data;
    }

    public function __toString()
    {
        $string = "{";
        foreach($this->data as $key  => $val){
            $string = $string . ("Species: " .$key . " => " . "Number: " . $val . ',');
        }
        return $string ."}";
    }

}