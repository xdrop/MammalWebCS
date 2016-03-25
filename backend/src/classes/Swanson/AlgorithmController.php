<?php


class AlgorithmController
{

    /***
     * Runs the algorithm on the first $n images
     * @param $n integer Algorithm is run for all ids <= $n
     */
    public function runAlgorithm($n,$store) {
        $id = 1;
        while($id <= $n){
            $this->runOnImage($id,$store);
        }
    }

    /**
     * @param $imageId integer Run the algorithm on a single image
     * @param boolean $store Store the result
     * @return array|string The result
     */
    public function runOnImage($imageId,$store){
        $classifier = new MammalClassifier();
        if($store){
            return $classifier->on($imageId)->classify()->store()->getResult();
        } else{
            return $classifier->on($imageId)->classify()->getResult();
        }
    }

    public function clearResults(){
        $query = new ClassificationQuery();
        $query->with(["all" => true])->delete();
    }

}