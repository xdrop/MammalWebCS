<?php


include('../../src/core.php');


class FilterQueryTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
    }

    function testSpeciesInclude()
    {
        $t = new FilterQuery();

        $result = $t->with(["species_include" => [22, 23]])->fetch();

        foreach ($result as $entry) {
            $this->assertTrue($entry['species'] == 22 || $entry['species'] == 23);
        }
    }


    function testSpeciesExclude()
    {
        $t = new FilterQuery();

        $result = $t->with(["species_exclude" => [22, 23]])->fetch();

        foreach ($result as $entry) {
            $this->assertTrue($entry['species'] != 22 || $entry['species'] != 23);
        }
    }
}