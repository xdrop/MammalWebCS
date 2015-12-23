<?php


class Utils
{
    public static function findMedian(array $list){
        rsort($list);
        $middle = round(count($list) / 2);

        return $list[(int)$middle-1];
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
}