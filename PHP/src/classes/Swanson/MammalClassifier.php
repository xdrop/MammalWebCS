<?php


/* Starting class implementing basic algorithm functions */


class MammalClassifier
{

    private $result;

    private $imageId;

    private $db;

    private $dataset;

    private $votesPerSpecies;

    /* Unused for now */

    const CONSECUTIVE_EXPECTED = 10;

    const NOTHING_HERE_IDENTIFIER = "nothing_here";

    // TODO: eventually switch these to numbers to store in a field in database

    const NOT_ENOUGH_TO_CLASSIFY = "not enough for classification";

    const FLAGGED_FOR_SCIENTIST = "Low evenness flagging for scientist";

    const VOTES_BEFORE_CONSENSUS = 25;

    const EVENNESS_THRESHOLD = 0.5;

    /**
     * MammalClassifier constructor.
     */
    public function __construct()
    {
        $this->result = null;
        $this->imageId = null;
        $this->db = new ClassificationQuery();
    }

    /**
     * Allows the classifier to operate on an explicit dataset
     * @param $dataset / The dataset
     * @return $this
     */
    public function onDataSet(&$dataset){
        if($dataset instanceof Classification){
            $this->dataset = $dataset;
        } else{
            $arr = [];
            foreach($dataset as $item){
                $arr[] = new Classification($item);
            }
            $this->dataset = &$arr;
        }
        return $this;
    }

    public function on($imageId)
    {
        $this->imageId = $imageId;
        $this->dataset = $this->db->with(['imageId' => $imageId])->fetch();
        $this->result = null;
        return $this;
    }

    /**
     * @return array -> Array which contains Species => [] of count of species in image
     *                  eg. Girrafe: [2,3,2,1] (users said giraffe appeared 2,3... times in image
     */
    private function mapOfSpeciesToCount()
    {

        /* Create a table to record the vote counts */

        $map = [];

        /* For each classification */

        foreach ($this->dataset as $classifications) {

            foreach ($classifications as $species => $count) {
                $map[$species][] = $count;
            }

        }

        /* Return the results */

        return $map;
    }


    /**
     * Checks if there are x number of consecutive classifications
     *
     * @param array $classifications / A list with classifications
     * @param $nonConsecutiveCount / The number of consecutive elements required
     * @return bool True if x consecutive, false otherwise
     */
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

                    return $current->toArray();
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
     * @param $dataset -> The data set in the format [[[species => number],[species => number]...], ...]
     * @param $consecutiveLim -> The number of consecutive we need
     * @param $type -> The type to evaluate consecutive for
     *              eg. 'nothing_here' will check evaluate that the first
     *              $consecutiveLim entries are 'nothing_here'
     * @return bool -> True if they are consecutive false otherwise
     */
    public function checkConsecutive($dataset, $consecutiveLim, $type)
    {
        $lastVote = null;

        foreach ($dataset as $vote) {
            if ($consecutiveLim > 0) {
                if ($lastVote == null || $vote->hashed() == $lastVote) {
                    $lastVote = $vote->hashed();
                    $consecutiveLim--;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        }
        return true;
    }

    public function getResult()
    {
        return $this->result;
    }

    /**
     * Stores the result of the algorithm in the database
     */
    public function store()
    {
        if($this->result == null){
            throw new RuntimeException('You need to call classify before storing the results!');
        }
        // TODO: Complete when db integration is done
//      $this->db->store(['classification' => $this->result);
    }

    /**
     * Removes the blank votes from the dataset
     */
    private function filterBlankVotes()
    {
        foreach ($this->dataset as $classification) {
            if (isset($classification[self::NOTHING_HERE_IDENTIFIER])) {
                unset($classification[self::NOTHING_HERE_IDENTIFIER]);
            }
        }
    }

    private function getVotesPerSpecies()
    {

        $mapSpeciesToVotes = [];

        foreach ($this->dataset as $classification) {
            // Amend here if you want to take the count into account
            foreach ($classification as $species => $count) {
                if (isset($mapSpeciesToVotes[$species])) {
                    $mapSpeciesToVotes[$species] += 1;
                } else {
                    $mapSpeciesToVotes[$species] = 1;
                }
            }
        }

        return $mapSpeciesToVotes;
    }

    private function listOfAnimalCounts()
    {
        $counts = [];

        foreach ($this->dataset as $classification) {
            $counts[] = count($classification);
        }

        return $counts;
    }

    private function evenness()
    {
        /* Disregard any blank votes as we no longer need them */

        $this->filterBlankVotes();

        /* Get a map in the form of Species => Number of users who classified that species */

        $this->votesPerSpecies = &$this->getVotesPerSpecies();


        /* $map is an alias for that */


        $map = &$this->votesPerSpecies;


        /* S is the different number of species */


        $S = count($map);

        if($S == 1){
            return 1;
        }


        /* Total votes are the total votes for all species */


        $totalVotes = array_sum($map);


        /* Calculate Pielou's evenness index */


        $sum = 0;

        foreach ($map as $currentSpecies => $votes) {
            $p_i = $votes / $totalVotes;

            $sum += $p_i * log($p_i);
        }

        return -$sum / log($S);
    }


    private function plurality()
    {

        /* Find the median of animal counts */

        $n = Utils::findMedian($this->listOfAnimalCounts());

        /* Get a map in the form of Species => Number of users who classified that species */

        $votesPerSpecies = $this->votesPerSpecies;

        /* Sort it by number of votes (descending) */

        arsort($votesPerSpecies);

        /* Get a list of the most-popularly voted species */

        $mostFrequentSpecies = array_keys($votesPerSpecies);

        $species = [];

        /* Set our selection to the the top n ones */

        for ($i = 0; $i < $n; $i++) {
            $species[] = $mostFrequentSpecies[$i];
        }

        /* Get a map in the form of Species => List of number of times appears in image */

        $mapOfSpeciesToCount = $this->mapOfSpeciesToCount();

        /* Create the final answer by finding the median of this count for each species */

        $finalAnswer = [];
        foreach ($species as $current) {
            $finalAnswer[$current] = Utils::findMedian($mapOfSpeciesToCount[$current]);
        }

        return $finalAnswer;
    }


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

                if ($consecutiveMatch) {
                    $this->result = $consecutiveMatch;
                } else {

                    /* Wait until we have 25 (VOTES_BEFORE_CONSENSUS) */

                    if ($numberOfVotes >= self::VOTES_BEFORE_CONSENSUS) {

                        /* Calculate evenness */

                        $evenness = $this->evenness();
                        print("Evenness calculated: " . $evenness . "\n");

                        // If evenness greater than the threshold run plurality else flag for scientist;
                        $this->result = $evenness > self::EVENNESS_THRESHOLD ? $this->plurality()
                                                                             : self::FLAGGED_FOR_SCIENTIST;

                    } else {
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

