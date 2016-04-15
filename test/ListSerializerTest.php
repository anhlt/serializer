<?php
use serializer\BooleanField;
use serializer\HTMLDict;
use serializer\IntegerField;
use serializer\ListSerializer;
use serializer\Serializer;

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
        $this->assertEquals($output, $serializer->validated_data);
    }

    function test_validate_html_input()
    {
        $data = new HTMLDict(array('[0]' => 1, '[1]' => 2));
        $output = [1, 2];
        $serializer = new IntegerListSerializer(null, $data);
        $this->assertTrue($serializer->is_valid());
        $this->assertEquals($output, $serializer->validated_data);
    }
}

class ChildSerializer extends Serializer
{
    public function define_fields()
    {
        $this->fields[ 'integer' ] = new IntegerField(array('require' => true));
        $this->fields[ 'boolean' ] = new BooleanField(array('require' => true));
    }
}

class NestedListSerializer extends ListSerializer
{
    public function define_fields()
    {
        $this->child = new ChildSerializer();
    }
}

class TestListSerializerContainNestedSerializer extends PHPUnit_Framework_TestCase
{
    function test_validate()
    {
        $input = [
            ['integer' => '123', 'boolean' => 'true'],
            ['integer' => '456', 'boolean' => 'false'],
        ];
        $output = [
            ['integer' => 123, 'boolean' => true],
            ['integer' => 456, 'boolean' => false],
        ];

        $serializer = new NestedListSerializer(null, $input);
        $this->assertTrue($serializer->is_valid());
        $this->assertEquals($output, $serializer->validated_data);
    }
}
