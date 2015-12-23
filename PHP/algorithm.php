<?php


/* Starting class implementing basic algorithm functions */


class MammalClassifier
{

    private $result;

    private $imageId;

    private $dbIntegrator;

    private $dataset;

    /* Unused for now */

    const CONSECUTIVE_EXPECTED = 10;

    const NOTHING_HERE_IDENTIFIER = 0;

    const NOT_ENOUGH_TO_CLASSIFY = -1;

    /**
     * MammalClassifier constructor.
     */
    public function __construct()
    {
        $this->result = null;
        $this->imageId = null;
//      $this->dbIntegrator = new DBIntegration();
    }


    /**
     * Returns a table with vote counts for each animal classification
     *
     * @param array $classifications A list of classifications
     * @return array A table with the vote counts of each animal
     */
    private function tallyVotes(array $classifications)
    {

        /* Create a table to record the vote counts */

        $table = [];

        /* For each classification */

        foreach ($classifications as $classification) {

            /* Did we already record some vote of the same type? */

            if (isset($table[$classification])) {

                /* If yes just increase the vote count for that animal */

                $table[$classification] += 1;

            } else {

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
     * @param $nonConsecutiveCount The number of consecutive elements required
     * @return bool True if x consecutive, false otherwise
     */

    // [ [ [species => number], [species => number], ... ], ... ]
    private function checkForNonConsecutive(array $classifications, $nonConsecutiveCount)
    {

        $consecutiveCountMap = [];

        /* Remember the last classification
         * seen starting with null */

        foreach ($classifications as $current) {

            /* If current is not same as last with the exception
             * of the first one then return false */


            if (isset($consecutiveCountMap[$current])) {
                $mapVal = $consecutiveCountMap[$current];

                /* If we found 10 consecutive */
                if ($mapVal >= $nonConsecutiveCount - 1) {

                    /* Just return consecutive classification */

                    return $current;
                } else {

                    /* Increment the number of times we've seen this one */

                    $consecutiveCountMap[$current] += 1;
                }
            } else {

                /* This is the first time we've seen this */

                $consecutiveCountMap[$current] = 1;
            }

        }

        return false;


    }

    /**
     * Aggregates the species counts
     *
     * @param array $classifications A list of classifications [[species,numberIdentified],[species,numberIdentified]]
     * @return array list Classification->[Number of species], example: Giraffe => [1,2,2,2,1]
     */
    public function getSpeciesCounts(array $classifications)
    {
        $table = [];

        /* Loop through the list of classifications getting tuples in the form of
         * (typeOfSpecies,numberIdentified) */

        foreach ($classifications as $classification) {

            /* Extract the type and number */

            $species = $classification[0];
            $number = $classification[1];

            /* Check if we've stored anything for this type of animal before before */

            if (!isset($table[$species])) {

                /* If not assign to a list with the classifications */

                $table[$species] = [$number];

            } else {

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
    public function getPercentageOfTrueFalse(array $list)
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
     * @param $dataset -> The data set in the format [[[species => number],[species => number]...], ...]
     * @param $consecutiveLim -> The number of consecutive we need
     * @param $type -> The type to evaluate consecutive for
     *              eg. 'nothing_here' will check evaluate that the first
     *              $consecutiveLim entries are 'nothing_here'
     * @return bool True if they are consecutive false otherwise
     */
    public function checkConsecutive($dataset, $consecutiveLim, $type)
    {
        $lastVote = null;

        foreach ($dataset as $vote) {
            if ($consecutiveLim > 0) {
                if ($lastVote == null || $vote == $lastVote) {
                    $lastVote = $vote;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
    }

    public function getResult()
    {

    }

    public function store()
    {
        // TODO: Complete when db integration is done
//      $this->dbIntegrator->store($this->result);
    }

    public function on($imageId)
    {
        $this->imageId = $imageId;
        //$this->dataset = $this->dbIntegrator->fetch($imageId);
        $this->dataset = [
            [['buffalo' => 3],['giraffe' => 2]],
            [['giraffe' => 3]],
            [['elephant' => 2]],
            [['giraffe' => 3],['buffalo' => 2] ]
        ];
        // TODO: This is just a testing example until database intergration is complete
        $this->result = null;
        return $this;
    }

    /*
     * [ [[species => number], [species => number], ... ], ... ]
     *
     * */
    public function classify()
    {
        $dataset = $this->dataset;
        $numberOfVotes = count($dataset);
        if ($numberOfVotes >= 5) {

            /* If first five consecutive were nothing here */

            if ($this->checkConsecutive($dataset, 5, self::NOTHING_HERE_IDENTIFIER)) {
                $this->result = self::NOTHING_HERE_IDENTIFIER;

            } else {
                $speciesCounts = $this->getSpeciesCounts($dataset);

            }
        } else {
            $this->result = self::NOT_ENOUGH_TO_CLASSIFY;
        }

        return $this;
    }

}

