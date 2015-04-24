<?php
/**
 * Created by PhpStorm.
 * User: letuananh
 * Date: 4/24/15
 * Time: 18:03
 */

use serializer\Serializer;
use serializer\CharField;
use serializer\IntegerField;


class TestSerializer extends  Serializer{
    public function define_fields()
    {
        $this->fields['name'] = new CharField(array('allow_blank' => false));
        $this->fields['age'] = new IntegerField(array('require' => true));
    }
}


class SerializersTest extends PHPUnit_Framework_TestCase {


    public function setUp(){
    }

    public function test_abc(){
        $this->assertEquals(0,0);
    }

    public function test_validate(){
        $data = ['name'=> 'anhlt', 'age'=> 2];
        $serializer = new TestSerializer(null,$data);
        $this->assertTrue($serializer->is_valid());
        $this->assertEquals($serializer->validated_data , $data);
    }
}