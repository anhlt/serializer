<?php
use serializer\ListSerializer;
use serializer\IntegerField;
use serializer\HTMLDict;

class IntegerListSerializer extends ListSerializer
{
    public function define_fields()
    {
        $this->child = new IntegerField();
    }
}

class ListSerializersTest extends PHPUnit_Framework_TestCase
{

    function setUp()
    {

    }

    function test_validate()
    {
        $data = ["123", "456"];
        $output = [123, 456];
        $serializer = new IntegerListSerializer(null, $data);
        $this->assertTrue($serializer->is_valid());
        $this->assertEquals( $output, $serializer->validated_data );
    }

    function test_validate_html_input()
    {
        $data = new HTMLDict(array('[0]' => 1, '[1]' => 2));
        $output = [1 , 2];
        $serializer = new IntegerListSerializer(null, $data);
        $this->assertTrue($serializer->is_valid());
        $this->assertEquals( $output, $serializer->validated_data );
    }
}

