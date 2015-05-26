<?php
use serializer\ListSerializer;
use serializer\IntegerField;

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
        assertTrue($serializer->is_valid());
        assert($serializer->validated_data == $output);
    }
}