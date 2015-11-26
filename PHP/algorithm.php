<?php


class MammalClassifier
{

    /**
     * Returns a table with vote counts for each animal classification
     *
     * @param array $classifications A list of classifications
     * @return array A table with the vote counts of each animal
     */
    public function tallyVotes(array $classifications){

        /* Create a table to record the vote counts */

        $table = [];

        /* For each classification */

        foreach ($classifications as $classification ){

            /* Did we already record some vote of the same type? */

            if(isset($table[i])){

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

    public function onVote()
    {

    }

}