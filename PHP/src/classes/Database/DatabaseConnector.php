<?php


class DatabaseConnector
{
    private static $pdo = null;

    public static function getDatabase()
    {
        $settings = Environment::databaseSettings();
        $PDO = new PDO($settings['driver'] .":host=" . $settings['host'] . ";port=". $settings['port']
            .";dbname=" . $settings['name'], $settings['username'], $settings['password']);
        if(is_null(self::$pdo)){
            self::$pdo = new FluentPDO($PDO);
        }
        return self::$pdo;
    }

}