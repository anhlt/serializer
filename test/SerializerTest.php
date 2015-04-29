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

    public function test_create(){
        $data = ['name'=> 'anhlt', 'age'=> 2];
        $serializer = new TestSerializer(null,$data);
        $this->assertTrue($serializer->is_valid());
        $obj =  $serializer->save();
        $this->assertEquals($obj->name , 'anhlt');
    }

    public function test_update(){
        $data = ['name'=> 'anhlt', 'age'=> 2];
        $obj = new \serializer\BasicObject(array('name'=> 'anhlt', 'age'=> 21));
        $serializer = new TestSerializer($obj, $data);
        $this->assertTrue($serializer->is_valid());
        $serializer->save();
        $this->assertEquals($obj->age, 2);
    }

    public function test_missing_value(){
        $data = ['name'=> 'anhlt'];
        $serializer = new TestSerializer(null, $data);
        $this->assertFalse($serializer->is_valid());
        $this->assertEquals($serializer->errors, 'This field is required.\n');
    }
}