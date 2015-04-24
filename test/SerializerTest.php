<?php
/**
 * Created by PhpStorm.
 * User: letuananh
 * Date: 4/24/15
 * Time: 18:03
 */

use serializer\Serializer;
use serializer\CharField;


class TestSerializer extends  Serializer{
    public function define_fields()
    {
        $this->fields['name'] = new CharField(array('allow_blank' => false));
    }
}


class SerializersTest extends PHPUnit_Framework_TestCase {


    public function setUp(){
        $this->serializer = new TestSerializer();
    }

    public function test_abc(){
        $this->assertEquals(0,0);
    }

    public function test_create(){

    }

}