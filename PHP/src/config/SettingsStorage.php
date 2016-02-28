<?php

class SettingsStorage
{
    const filename = 'scientist_settings.json';


    /**
     * Returns the settings stored at the scientist_settings.json file
     *
     * @return array
     */
    public static function settings(){
        $fileData = file_get_contents(dirname(__FILE__). "/" .self::filename);
        $data = json_decode($fileData,true);
        return $data;
    }


    /**
     * Updates the whole file with the settings
     *
     * @param $settings array The new settings array
     */
    public static function update($settings){
        file_put_contents(dirname(__FILE__). "/" .self::filename, json_encode($settings));
    }

    /**
     * Sets a scientist parameter and updates the file
     * @param $params
     */
    public static function set($params){
        $settings = self::settings();

        foreach($params as $key => $value){

            /* Don't add a setting that doesn't exist */

            if(array_key_exists($key, $settings)){
                $settings[$key] = $value;
            }
        }

        self::update($settings);
    }


}