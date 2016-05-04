<?php


class AlgorithmController
{

    /**
     * Run the algorithm as a background job
     * @param $statusQuery JobStatusQuery
     * @param array $settings The settings to run the algorithm with
     * @param bool $scientistDataset Whether to store in the scientist dataset table
     * @param int $from Id of image to run the algorithm from
     * @param int $to Id of the image to run the algorithm to
     */
    public function runAlgorithmJob($statusQuery, $settings, $scientistDataset = false, $from = 1, $to = -1)
    {
        $classifier = new MammalClassifier($settings,$scientistDataset);
        $id = $from;
        if ($to < 0) {
            $to = $this->getMaxImageId();
        }
        $statusQuery->with(["started" => true,'progress' => 0, "total" => $to])->update();
        while ($id <= $to) {
            if ($id % 500 == 0) {
                $statusQuery->with(["started" => true, "progress" => $id])->update();
            }
            $this->runOnImage($id, true, $classifier);
            $id++;
        }
        $statusQuery->with(["started" => false, "progress" => $to])->update();
    }



    /**
     * Run the algorithm as a background job
     * @param $statusQuery JobStatusQuery
     * @param array $settings The settings to run the algorithm with
     * @param bool $scientistDataset Whether to store in the scientist dataset table
     * @param int $from Id of image to run the algorithm from
     * @param int $to Id of the image to run the algorithm to
     */
    public function runAlgorithmJobBatch($statusQuery, $settings, $scientistDataset = false, $from = 1, $to = -1)
    {
        $classifier = new MammalClassifier($settings,$scientistDataset);
        $classQuery = new BatchClassificationQuery();

        $id = $from;
        if ($to < 0) {
            $to = $this->getMaxImageId();
        }
        $classifications = $classQuery->with(['from' => $from, 'to' => $to])->fetch()->asArray();
        $total = count($classifications);
        $statusQuery->with(["started" => true,'progress' => 0, "total" => $total])->update();

        foreach($classifications as $class){
            if ($id % 500 == 0) {
                $statusQuery->with(["started" => true, "progress" => $id])->update();
            }
            $this->runOnClassification($id, true, $classifier,$class);
            $id++;
        }

        $statusQuery->with(["started" => false, "progress" => $to])->update();
    }

    /**
     * @param $imageId integer Run the algorithm on a single image
     * @param boolean $store Store the result
     * @param MammalClassifier|null $classifier An instance of MammalClassifier
     * @param bool $scientistDataset Whether to store in the scientist dataset table
     * @return array|string The result
     */
    public function runOnImage($imageId, $store, $classifier, $scientistDataset = false)
    {
        if(!$classifier){
            $classifier = new MammalClassifier(null, $scientistDataset);
        }
        try {
            if ($store) {
                return $classifier->on($imageId)->classify()->store()->getResult();
            } else {
                return $classifier->on($imageId)->classify()->getResult();
            }
        } catch (Exception $e) {
            return "No classifications.";
        }
    }


    /**
     * @param $imageId integer Run the algorithm on a single image
     * @param boolean $store Store the result
     * @param MammalClassifier|null $classifier An instance of MammalClassifier
     * @param $classification
     * @param bool $scientistDataset Whether to store in the scientist dataset table
     * @return array|string The result
     */
    public function runOnClassification($imageId, $store, $classifier, $classification, $scientistDataset = false)
    {
        if(!$classifier){
            $classifier = new MammalClassifier(null, $scientistDataset);
        }
        try {
            if ($store) {
                return $classifier->onDataSet($classification)->classify()->store()->getResult();
            } else {
                return $classifier->onDataSet($classification)->classify()->getResult();
            }
        } catch (Exception $e) {
            return "No classifications.";
        }
    }


    /**
     * Clears previously stored results
     * @param bool $scientistDataset
     */
    public function clearResults($scientistDataset = false)
    {
        $query = new ClassificationQuery();
        $query->with(["all" => true,
            "scientist_dataset" => $scientistDataset])
            ->delete();
    }

    /**
     * Returns the maximum image id (id of the last image)
     */
    public function getMaxImageId()
    {
        return DatabaseConnector::getPurePDO()->query("SELECT MAX(photo_id) FROM animal")
            ->fetch()[0];
    }

}
