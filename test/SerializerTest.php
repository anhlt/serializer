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

class test{
    public function append_array($current_array, $value_array, $value){
        $current_key = array_shift($value_array);
        if(count($value_array) == 0)
            $current_array = array($current_key => $value);
        else
            $current_array[$current_key] = $this->append_array(array(),$value_array, $value);
        return $current_array;
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