<?php


class ImageURL
{

    public static function getURLFromId($imageId)
    {
        $query = new ImageInformationQuery();
        $results = $query->with(['imageId' => $imageId])->fetch();
        $personId = $results['person_id'];
        $siteId = $results['site_id'];
        $filename = $results['filename'];
        return self::getURL($personId,$siteId,$filename);
    }

    public static function getURL($personId, $siteId, $filename)
    {
        return 'http://www.mammalweb.org/biodivimages/person_' . $personId . '/site_' . $siteId . '/' . $filename;
    }
}