<?php


include('../../src/core.php');


class FilterQueryTest extends \PHPUnit_Framework_TestCase
{
    function setUp(){
    }

    function testHasURL(){
        $t = new FilterQuery();

        $result = $t->with([])->fetch()->asArray();

        $this->assertArrayHasKey("url",$result[0]);
    }

    function testURLisCorrect()
    {
        $t = new FilterQuery();

        $result = $t->with([])->fetch()->asArray();

        $url = $result[0]['url'];

        $this->assertTrue(substr($url, 0, 4) === "http");
    }


    function testSpeciesInclude()
    {
        $t = new FilterQuery();

        $result = $t->with(["species_include" => [22, 23]])->fetch()->asArray();

        foreach ($result as $entry) {
            $this->assertTrue($entry['species'] == 22 || $entry['species'] == 23);
        }
    }


    function testSpeciesExclude()
    {
        $t = new FilterQuery();

        $result = $t->with(["species_exclude" => [22, 23]])->fetch()->asArray();

        foreach ($result as $entry) {
            $this->assertTrue($entry['species'] != 22 || $entry['species'] != 23);
        }
    }
}