<?php


class ChartStatsQuery extends Query
{

    /**
     * Creates the SQL query to fetch the data required and stores it in the appropriate internal query
     * @param array $params The parameters of the query
     * @return mixed
     */
    protected function fetchQuery(&$params)
    {
        $query = $this->db->from('classified')->select(['site.site_id','site.site_name AS site_name',
            'species.option_name as species_name','classified.species'])
                      ->leftJoin('photo ON photo.photo_id = classified.photo_id')
                      ->leftJoin('site ON photo.site_id = site.site_id')
                      ->leftJoin('options AS species ON classified.species = species.option_id')
                      ->limit(10000);

        $this->addFetchQuery($query);
    }

    protected function reformat($results)
    {
        $map = [];
        foreach($results as $entry){
            $species = $entry['species_name'];
            $habitat = $entry['site_name'];

            if($entry['species'] == 86){
                continue;
            }

            if(!isset($map[$species])){
                $habitatCount = [];
                $habitatCount[$habitat] = 1;
                $map[$species]['freq'] = $habitatCount;
                $map[$species]['name'] = $species;
            } else{
                if(!isset($map[$species][$habitat])){
                    $map[$species]['freq'][$habitat] = 1;
                } else{
                    $map[$species]['freq'][$habitat] += 1;
                }
            }
        }
        $field = [];
        foreach($map as $entry){
            $field[] = ["name"=> $entry['name'], "freq" => $entry['freq']];
        }
        return $field;
    }


}