<?php

class SettingsStorage
{
    const filename = 'scientist_settings.json';


    public static function settings(){
        $fileData = file_get_contents(dirname(__FILE__). "/" .self::filename);
        $data = json_decode($fileData,true);
        return $data;
    }



    public static function update($settings){
        file_put_contents(self::filename, json_encode($settings));
    }


}