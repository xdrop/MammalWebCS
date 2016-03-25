<?php


class AlgorithmController
{

    /***
     * Runs the algorithm on the first $n images
     * @param boolean [$store] Store the results
     * @param bool [$log] Output log
     * @param $n integer Algorithm is run for all ids <= $n
     */
    public function runAlgorithm($store = true, $log = false, $n = -1)
    {
        @ini_set('max_execution_time', 300);
        print("<pre>");
        $id = 1;
        if ($n < 0) {
            $n = $this->getMaxImageId();
        }
        while ($id <= $n) {
            if ($id % 1000 == 0 || $log) {
                print("=> Running on image id " . $id . ":\n");
                print($this->runOnImage($id, $store));
                print("\n");
                ob_flush();
            } else {
                $this->runOnImage($id, $store);
            }
            $id++;
        }
        print("</pre>");
    }

    /**
     * @param $imageId integer Run the algorithm on a single image
     * @param boolean $store Store the result
     * @return array|string The result
     */
    public function runOnImage($imageId, $store)
    {
        $classifier = new MammalClassifier();
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

    public function clearResults()
    {
        $query = new ClassificationQuery();
        $query->with(["all" => true])->delete();
    }

    protected function getMaxImageId()
    {
        return DatabaseConnector::getPurePDO()->query("SELECT MAX(photo_id) FROM animal")
            ->fetch()[0];
    }

}