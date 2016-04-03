<?php


class Environment
{

    const DATABASE_SETTINGS_FILE = "db_settings.env.ini";
    const DATABASE_SETTINGS_FILE_LOCAL = "db_settings.local.ini";


    private static function settingsFile(){
        if(file_exists(dirname(__FILE__). "\\" . self::DATABASE_SETTINGS_FILE_LOCAL)){
            $settings = parse_ini_file(self::DATABASE_SETTINGS_FILE_LOCAL,true);
        } else if(file_exists(dirname(__FILE__). "/" . self::DATABASE_SETTINGS_FILE_LOCAL)){
            $settings = parse_ini_file(self::DATABASE_SETTINGS_FILE_LOCAL,true);
        } else{
            $settings = parse_ini_file(self::DATABASE_SETTINGS_FILE,true);
        }
        return $settings;
    }


    public static function databaseSettings(){
        $settings = self::settingsFile();
        return $settings['database'];
    }

    public static function siteSettings(){
      $settings = self::settingsFile();
      return $settings['site'];
    }

    public static function getRootDir(){
        return dirname(__DIR__);
    }




}