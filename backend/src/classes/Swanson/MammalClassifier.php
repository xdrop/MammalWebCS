<?php


/* Starting class implementing basic algorithm functions */


class MammalClassifier
{

    private $result;

    private $imageId;

    private $db;

    /**
     * @var Classification[]
     */
    private $dataset;

    private $votesPerSpecies;

    /**
     * @var bool Whether to store in scientist dataset results or public
     */
    private $scientistDataset;

    /* Unused for now */

    // 86 is the species used in the database for nothing here

    const NOTHING_HERE_IDENTIFIER = "86";

    const NOT_ENOUGH_TO_CLASSIFY = "not enough for classification";

    const FLAGGED_FOR_SCIENTIST = "High disagreement flag for scientist";


    /**
     * MammalClassifier constructor.
     * @param null $settings A settings object to run the algorithm
     * @param bool $scientistDataset Switches between running on live data, or scientist data
     */
    public function __construct($settings = null, $scientistDataset = false)
    {
        $this->result = null;
        $this->imageId = null;
        try {
            $this->db = new ClassificationQuery();
        } catch (PDOException $exception) {
            trigger_error("Failure to create a database connection." .
                " Possibly database settings provided are wrong", E_USER_WARNING);
        }

        if (!$settings) {
            $settings = SettingsStorage::settings();
        }
        $this->scientistDataset = $scientistDataset;
        
        $this->CONSECUTIVE_EXPECTED =
            Utils::getValue($settings['consecutive_expected'], 8);
        $this->VOTES_BEFORE_CONSENSUS =
            Utils::getValue($settings['votes_before_consensus'], 15);
        $this->UNREASONABLE_NUMBER_OF_SPECIES_IN_IMAGE =
            Utils::getValue($settings['unreasonable_number_of_species_in_image'], 5);
        $this->EVENNESS_THRESHOLD_COUNT =
            Utils::getValue($settings['evenness_threshold_count'], 0.69);
        $this->EVENNESS_THRESHOLD_SPECIES =
            Utils::getValue($settings['evenness_threshold_species'], 0.7);
        $this->NUMBER_OF_NOTHING_HERE_BEFORE_CLASSIFY = 
            Utils::getValue($settings["number_of_nothing_here_before_classify"],5);

    }

    /**
     * Allows the classifier to operate on an explicit dataset
     * @param $dataset / The dataset
     * @return $this
     */
    public function onDataSet(&$dataset)
    {
        if ($dataset[0] instanceof Classification) {
            $this->dataset = $dataset;
        } else {
            $arr = [];
            foreach ($dataset as $item) {
                $arr[] = new Classification($item);
            }
            $this->dataset = &$arr;
        }
        return $this;
    }

    public function on($imageId)
    {
        $this->imageId = $imageId;
        $query = $this->db->with(['imageId' => $imageId])->fetch();
        if ($query !== "none") {
            $this->dataset = $query->asArray();
        } else {
            throw new RuntimeException("Invalid image id supplied to on().");
        }

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

        /** @var Classification $current */
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
    private function checkConsecutive($dataset, $consecutiveLim, $type)
    {
        $lastVote = null;

        /** @var Classification $vote */
        foreach ($dataset as $vote) {
            if ($consecutiveLim > 0) {
                if (substr($vote->hashed(), 0, strlen($type . '=')) === ($type . '=')) {
                    if ($lastVote == null || $vote->hashed() == $lastVote) {
                        $lastVote = $vote->hashed();
                        $consecutiveLim--;
                    } else {
                        return false;
                    }
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
        if ($this->result) {
            return $this->result;
        } else {
            return "No classifications.";
        }
    }

    /**
     * Stores the result of the algorithm in the database
     * @param bool [$new] Add a new record instead of updating
     * @return $this
     */
    public function store($new = false)
    {
        if ($this->result === null) {
            throw new RuntimeException('You need to call classify before storing the results!');
        }
        if ($this->db === null) {
            throw new RuntimeException('You cannot store without an active database connection');
        }
        if ($new) {
            $this->db->with(
                ['imageId' => $this->imageId,
                    'result' => $this->result, 'scientist_dataset' => $this->scientistDataset])->store();
        } else {
            $this->db->with(['imageId' => $this->imageId,
                'result' => $this->result, 'scientist_dataset' => $this->scientistDataset])->update();
        }
        return $this;
    }

    /**
     * Removes the blank votes from the dataset
     */
    private function filterBlankVotes()
    {
        for ($i = 0; $i < count($this->dataset); ++$i) {
            if (isset($this->dataset[$i][self::NOTHING_HERE_IDENTIFIER])) {
                $this->dataset[$i]->remove(self::NOTHING_HERE_IDENTIFIER);
            }
        }
    }


    private function filterUnreasonableVotes()
    {
        for ($i = 0; $i < count($this->dataset); ++$i) {
            if ($this->dataset[$i]->sum() > $this->UNREASONABLE_NUMBER_OF_SPECIES_IN_IMAGE) {
                unset($this->dataset[$i]);
            }
        }
        $this->dataset = array_values($this->dataset);
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

    private function getNumberOfAnimalsFrequency()
    {
        $mapNumberToFrequency = [];

        foreach ($this->dataset as $classification) {
            $sum = $classification->sum();
            if (isset($mapNumberToFrequency[$sum])) {
                $mapNumberToFrequency[$sum] += 1;
            } else {
                $mapNumberToFrequency[$sum] = 1;
            }
        }

        if (isset($mapNumberToFrequency[0])) unset($mapNumberToFrequency[0]);


        return $mapNumberToFrequency;
    }

    private function listOfSpeciesCounts()
    {
        $counts = [];

        foreach ($this->dataset as $classification) {
            $counts[] = count($classification);
        }

        return $counts;
    }

    /**
     * @param bool $evennessType True for evenness of species type / false for
     * @return float|int
     */
    private function pielousEvenness($evennessType)
    {
        /* Get a map in the form of Species/NumberOfAnimals => Number of times it appears */
        if ($evennessType) {
            $map = $this->getVotesPerSpecies();
            $this->votesPerSpecies = $map;
        } else {
            $map = $this->getNumberOfAnimalsFrequency();
        }

        /* S is the different number of species */

        $S = count($map);

        if ($S == 1) {
            // all in agreement return 0
            return 0;
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


    private function evenness()
    {
        /* Disregard any blank votes as we no longer need them */

        $this->filterBlankVotes();

        $evennessOnSpecies = $this->pielousEvenness(true);

        $evennessOnAnimalCount = $this->pielousEvenness(false);

        return ['countEvenness' => $evennessOnAnimalCount, 'speciesEvenness' => $evennessOnSpecies];

    }


    private function plurality()
    {
        /* Find the median of animal counts */

        $n = Utils::findMedian($this->listOfSpeciesCounts());

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
        $dataset = &$this->dataset;
        if ($dataset === null) {
            return $this;
        }
        $this->filterUnreasonableVotes();
        $numberOfVotes = count($dataset);
        if ($numberOfVotes >= $this->NUMBER_OF_NOTHING_HERE_BEFORE_CLASSIFY) {

            /* If first X consecutive were nothing here */

            if ($this->checkConsecutive($dataset, $this->NUMBER_OF_NOTHING_HERE_BEFORE_CLASSIFY, self::NOTHING_HERE_IDENTIFIER)) {
                /* classify as nothing here */
                $this->result = [
                    'classification' => [self::NOTHING_HERE_IDENTIFIER => 0],
                    'evenness_species' => 0,
                    'evenness_count' => 0
                ];

            } else {

                /* We check that we have 10 matching classifications of any kind */

                $consecutiveMatch = $this->checkForNonConsecutive($this->dataset, $this->CONSECUTIVE_EXPECTED);

                /* Calculate evenness */

                $evenness = $this->evenness();

                /* If 10 matching found then classify */

                if ($consecutiveMatch) {
                    $this->result = [
                        'classification' => $consecutiveMatch,
                        'evenness_species' => $evenness['speciesEvenness'],
                        'evenness_count' => $evenness['countEvenness']
                    ];
                } else {

                    /* Wait until we have 25 (VOTES_BEFORE_CONSENSUS) */

                    if ($numberOfVotes >= $this->VOTES_BEFORE_CONSENSUS) {

                        // If evenness greater than the threshold run plurality else flag for scientist;
                        $this->result = $evenness['speciesEvenness'] < $this->EVENNESS_THRESHOLD_SPECIES &&
                        $evenness['countEvenness'] < $this->EVENNESS_THRESHOLD_COUNT
                            ? [
                                'classification' => $this->plurality(),
                                'evenness_species' => $evenness['speciesEvenness'],
                                'evenness_count' => $evenness['countEvenness']
                            ]
                            :
                            [
                                'classification' => self::FLAGGED_FOR_SCIENTIST,
                                'evenness_species' => $evenness['speciesEvenness'],
                                'evenness_count' => $evenness['countEvenness']
                            ];

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

