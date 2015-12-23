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

    const VOTES_BEFORE_CONSENSUS = 25;

    const EVENNESS_THRESHOLD = 12;

    const FLAGGED_FOR_SCIENTIST = 1;

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

            if (isset($table[$classification->hashed()])) {

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
     * @param array $classifications / A list with classifications
     * @param $nonConsecutiveCount / The number of consecutive elements required
     * @return bool True if x consecutive, false otherwise
     */

    // [ [ [species => number], [species => number], ... ], ... ]
    private function checkForNonConsecutive(array $classifications, $nonConsecutiveCount)
    {

        $consecutiveCountMap = [];

        /* Remember the last classification
         * seen starting with null */

        foreach ($classifications as $current) {

            $currentClassificationKey = $current->hashed();
            /* If current is not same as last with the exception
             * of the first one then return false */


            if (isset($consecutiveCountMap[$currentClassificationKey])) {
                $mapVal = $consecutiveCountMap[$currentClassificationKey];

                /* If we found 10 consecutive */
                if ($mapVal >= $nonConsecutiveCount - 1) {

                    /* Just return consecutive classification */

                    return $current;
                } else {

                    /* Increment the number of times we've seen this one */

                    $consecutiveCountMap[$currentClassificationKey] += 1;
                }
            } else {

                /* This is the first time we've seen this */

                $consecutiveCountMap[$currentClassificationKey] = 1;
            }

        }

        return false;


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
        return $this->result;
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

        // TODO: This is just a testing example until database intergration is complete
        $this->dataset = [];
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['buffalo' => 3,'giraffe' => 2]);
        $this->dataset[] = new Classification(['giraffe' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 3]);
        $this->dataset[] = new Classification(['elephant' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 2,'buffalo' => 3]);
        $this->dataset[] = new Classification(['giraffe' => 3,'buffalo' => 2]);

        $this->result = null;
        return $this;
    }

    private function filterBlankVotes(){
        foreach($this->dataset as $classification){
            if(isset($classification[self::NOTHING_HERE_IDENTIFIER])){
                unset($classification[self::NOTHING_HERE_IDENTIFIER]);
            }
        }
    }


    private function evenness(){
        $this->filterBlankVotes();


        return 10;
    }


    private function plurality(){
        return 'Antelope,3';
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

                /* We check that we have 10 matching classifications of any kind */

                $consecutiveMatch = $this->checkForNonConsecutive($this->dataset, self::CONSECUTIVE_EXPECTED);

                /* If 10 matching found then classify */

                if($consecutiveMatch){
                    $this->result = $consecutiveMatch;
                } else{
                    if($numberOfVotes >= self::VOTES_BEFORE_CONSENSUS){
                        $evenness = $this->evenness();

                        // If evenness greater than the threshold run plurality else flag for scientist;
                        $this->result = $evenness > self::EVENNESS_THRESHOLD ? $this->plurality()
                                                                             : self::FLAGGED_FOR_SCIENTIST;

                    } else{
                        $this->result = self::NOT_ENOUGH_TO_CLASSIFY;
                    }
                }

            }
        } else {
            $this->result = self::NOT_ENOUGH_TO_CLASSIFY;
        }

        return $this;
    }

}

