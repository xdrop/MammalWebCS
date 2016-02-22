<?php


class DatabaseConnector
{
    private static $pdo = null;

    public static function getDatabase()
    {
        if(is_null(self::$pdo)){
            $settings = Environment::databaseSettings();
            try {
                $PDO = new PDO($settings['driver'] . ":host=" . $settings['host'] . ";port=" . $settings['port']
                    . ";dbname=" . $settings['name'], $settings['username'], $settings['password']);
            } catch (PDOException $e){
                throw new DatabaseException();
            }
            self::$pdo = new FluentPDO($PDO);
        }
        return self::$pdo;
    }

}