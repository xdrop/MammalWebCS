<?php

class SettingsStorage
{
    const filename = 'scientist_settings.json';


    public static function settings(){
        $data = json_decode(file_get_contents(self::filename));
        return $data;
    }



    public static function update($settings){
        file_put_contents(self::filename, json_encode($settings));
    }


}