<?php


class FileStorage
{

    private static $storageDir = "storage";



    public static function outputFile($filename,$dir){
        $path = self::getPath($filename,$dir);
        if(file_exists($path) && is_file($path)) {
            readfile($filename);
        }
    }

    public static function fileToString($filename,$dir){
        return file_get_contents(self::getPath($filename,$dir));
    }

    /***
     * @param $filename string The filename
     * @param $dir string The directory under storage
     * @param $file File the file
     * @return bool|int
     */
    public static function storeFile($filename, $dir, $file){
        $path = self::getPath($filename, $dir);
        return file_put_contents($path,$file);
    }

    /**
     * @param $filename
     * @param $dir
     * @return string
     */
    public static function getPath($filename, $dir)
    {
        $path = Environment::getRootDir() . '/' . self::$storageDir . "/" . $dir . '/' . $filename;
        return $path;
    }

    public static function downloadFile($filename,$dir)
    {
        $path = self::getPath($filename, $dir);
        if (file_exists($path) && is_file($path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="output.csv"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit;
        }
    }

}