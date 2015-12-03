<?php


/* Starting class implementing basic algorithm functions */


class MammalClassifier
{

    /* Unused for now */

    const CONSECUTIVE_EXPECTED = 10;

    /**
     * Returns a table with vote counts for each animal classification
     *
     * @param array $classifications A list of classifications
     * @return array A table with the vote counts of each animal
     */
    private function tallyVotes(array $classifications){

        /* Create a table to record the vote counts */

        $table = [];

        /* For each classification */

        foreach ($classifications as $classification ){

            /* Did we already record some vote of the same type? */

            if(isset($table[$classification])){

                /* If yes just increase the vote count for that animal */

                $table[$classification] += 1;

            } else{

                /* Else initialize it to one*/

                $table[$classification] = 1;
            }
        }

        /* Return the results */

        return $table;
    }

    /**
     * Checks if there are x number of consecutive classifications
     *
     * @param array $classifications A list with classifications
     * @param $x The number of consecutive elements required
     * @return bool True if x consecutive, false otherwise
     */
    private function checkConsecutive(array $classifications, $x){

        /* Get the length of the array */

        $length = count($classifications);

        /* If we don't have enough to each x consecutive then
         * return false */

        if($length < $x){
            return false;
        } else{

            /* Remember the last classification
             * seen starting with null */

            $last = null;
            foreach ($classifications as $current){

                /* If current is not same as last with the exception
                 * of the first one then return false */

                if($last != null || $last != $current){
                    return false;
                }

                /* Set the last to current */

                $last = $current;
            }
        }

        /* If we've reached this stage then the first x classifications
         * are equal */

        return true;
    }

    /**
     * Aggregates the species counts
     *
     * @param array $classifications A list of classifications [[species,numberIdentified],[species,numberIdentified]]
     * @return array list Classification->[Number of species], example: Giraffe => [1,2,2,2,1]
     */
    public function getSpeciesCounts(array $classifications){
        $table = [];

        /* Loop through the list of classifications getting tuples in the form of
         * (typeOfSpecies,numberIdentified) */

        foreach ($classifications as $classification){

            /* Extract the type and number */

            $species= $classification[0];
            $number = $classification[1];

            /* Check if we've stored anything for this type of animal before before */

            if(!isset($table[$species])){

                /* If not assign to a list with the classifications */

                $table[$species] = [$number];

            } else{

                /* Get the current list of number identified for that species */

                $results = &$table[$species];

                /* Append to the end */

                $results[] = $number;
            }

        }
        return $table;
    }

    /**
     * @param array $list A list of true / false values
     * @return float The percentage of truth values
     */
    public function getPercentageOfTrueFalse(array $list){
        $numberOfTrue = 0;
        $total = 0;
        foreach($list as $truthvalue){
            if($truthvalue){
                $numberOfTrue++;
            }
            $total++;
        }
        return ($numberOfTrue/$total);
    }

    public function onVote()
    {

    }

}