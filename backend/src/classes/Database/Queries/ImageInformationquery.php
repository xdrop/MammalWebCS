<?php


class ImageInformationQuery extends Query{


    protected function fetchQuery(&$params)
    {
        if (!Utils::keysExist(['imageId'], $params)) {
            throw new BadMethodCallException("You need to provide a value to photoId before using this method.");
        }


        $photoId = $params['imageId'];

        $query = $this->db->from('photo')
                    ->select(['filename','site_id', 'person_id'])
                    ->where('photo_id',$photoId);

        $this->addFetchQuery($query);

    }


    protected function storeQuery(&$params){}

    protected function updateQuery(&$params){}

    protected function deleteQuery(&$params){}

    protected function reformat($results)
    {
        if(!is_null($results) && count($results) > 0){
            return $results[0];
        } else{
            return null;
        }
    }


}