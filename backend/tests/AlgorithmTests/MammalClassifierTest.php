<?php

include('../../src/core.php');
class MammalClassifierTest extends \PHPUnit_Framework_Testcase
{
    function setUp(){
        PHPUnit_Framework_Error_Warning::$enabled = FALSE;
    }

    function testNothingHere(){
        $m = new MammalClassifier();
        $testData = '[{"86":0},{"86":0},{"86":0},{"86":0},{"86":0},{"86":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":2},{"giraffe":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":2,"antelope":1},{"buffalo":2,"antelope":1},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"nothing_here":0},{"buffalo":1,"antelope":1},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2}]';
        $result = $m->onDataSet(json_decode($testData,true))->classify()->getResult();
        $this->assertArrayHasKey('classification',$result);
        $this->assertArrayHasKey(MammalClassifier::NOTHING_HERE_IDENTIFIER,$result['classification']);

    }

    function testMatching(){
        $m = new MammalClassifier();
        $testData = '[{"86":0},{"86":0},{"86":0},{"86":0},{"86":2},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"buffalo":1,"antelope":1},{"matched":3},{"buffalo":1,"antelope":1},{"matched":3},{"buffalo":1,"antelope":2},{"giraffe":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":2,"antelope":1},{"buffalo":2,"antelope":1},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"nothing_here":0},{"buffalo":1,"antelope":1},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2}]';
        $result = $m->onDataSet(json_decode($testData,true))->classify()->getResult();
        $this->assertArrayHasKey('classification',$result);
        $this->assertArrayHasKey('matched',$result['classification']);
        $this->assertEquals(3,$result['classification']['matched']);
    }

    function testEvenness(){
        $m = new MammalClassifier();
        $testData = '[{"86":0},{"86":0},{"86":0},{"86":0},{"86":2},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"matched":3},{"buffalo":1,"antelope":1},{"matched":3},{"buffalo":1,"antelope":1},{"matched":3},{"buffalo":1,"antelope":2},{"giraffe":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":2,"antelope":1},{"buffalo":2,"antelope":1},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"nothing_here":0},{"buffalo":1,"antelope":1},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2}]';
        $result = $m->onDataSet(json_decode($testData,true))->classify()->getResult();
        $this->assertArrayHasKey('classification',$result);
        $this->assertArrayHasKey('evenness_count',$result);
        $this->assertArrayHasKey('evenness_species',$result);
        $this->assertEquals(0.76854136901307,$result['evenness_species']);
        $this->assertEquals(0.99631651955896,$result['evenness_count']);
    }

    function testPlurality(){
        $m = new MammalClassifier();
        $testData = '[{"buffalo":1,"antelope":1},{"buffalo":2},{"cat":2},{"buffalo":1},{"cat":2},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":2},{"giraffe":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"buffalo":2,"antelope":1},{"buffalo":2,"antelope":1},{"antelope":2},{"buffalo":1,"antelope":1},{"buffalo":1,"antelope":1},{"nothing_here":0},{"buffalo":1,"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2},{"antelope":2}]';
        $result = $m->onDataSet(json_decode($testData,true))->classify()->getResult();
        $this->assertArrayHasKey('classification',$result);
        $this->assertArrayHasKey('antelope',$result['classification']);
        $this->assertArrayHasKey('buffalo',$result['classification']);
        $this->assertEquals(1,$result['classification']['antelope']);
        $this->assertEquals(1,$result['classification']['buffalo']);
    }
}