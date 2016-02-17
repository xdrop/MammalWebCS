<?php


class Utils
{
    public static function findMedian(array $list)
    {
        rsort($list);
        $middle = round(count($list) / 2);

        return $list[(int)$middle - 1];
    }

    /**
     * @param array $list A list of true / false values
     * @return float The percentage of truth values
     */
    public static function getPercentageOfTrueFalse(array $list)
    {
        $numberOfTrue = 0;
        foreach ($list as $truthvalue) {
            if ($truthvalue) {
                $numberOfTrue++;
            }
        }
        return ($numberOfTrue / count($truthvalue));
    }

    /**
     * @param $var mixed The value you want to access
     * @param null $default The default value if the key is not set
     * @return null The value or default if not found
     */
    public static function getValue(&$var, $default=null) {
        return isset($var) ? $var : $default;
    }

    /**
     * @param array $required The required keys
     * @param array $data The array that should contain the keys
     * @return bool True if all keys exist, false otherwise
     */
    public static function keysExist($required, $data)
    {
        if(is_array($required)){
            foreach ($required as $field) {
                if (!array_key_exists($field, $data)) return false;
            }
        } else{
            return array_key_exists($required,$data);
        }
        return true;
    }
}