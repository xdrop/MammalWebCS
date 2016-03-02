<?php


include('../../src/core.php');


class FilterQueryTest extends \PHPUnit_Framework_TestCase
{
    function setUp(){
    }

    function testHasURL(){
        $t = new FilterQuery();

        $result = $t->with([])->fetch();

        $this->assertArrayHasKey("url",$result[0]);
    }

    function testURLisCorrect(){
        $t = new FilterQuery();

        $result = $t->with([])->fetch();

        $url  = $result[0]['url'];

        $this->assertTrue(substr( $url, 0, 4 ) === "http");

    }
}