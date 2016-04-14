<?php

/**
 * Created by PhpStorm.
 * User: Le
 * Date: 4/26/2015
 * Time: 4:29 PM
 */
use Carbon\Carbon;
use serializer\CharField;
use serializer\DateTimeField;
use serializer\FloatField;
use serializer\SkipField;

class CharFieldTest extends PHPUnit_Framework_TestCase
{

    protected $field = null;
    protected $valid_mappings = array(
        1 => '1',
        'abc' => 'abc',
        'test' => 'test'
    );
    protected $invalid_mappings = array(
        '' => 'This field may not be blank'
    );

    function setUp()
    {
        $this->field = new CharField();
    }

    /**
     *
     */
    function test_valid()
    {
        foreach ($this->valid_mappings as $input => $expect) {
            $this->assertEquals($expect, $this->field->validate($input));
        }
    }
}

class DatetimeFieldTest extends PHPUnit_Framework_TestCase
{
    protected $field = null;
    protected $valid_mappings = array();
    protected $invalid_mappings = array(
        '' => 'This field may not be blank'
    );

    function setUp()
    {
        $this->field = new DateTimeField(array('allow_blank' => false));
        $this->valid_mappings = array(
            '1975-04-30 00:00:00' => Carbon::createFromFormat('Y-m-d H:i:s', '1975-04-30 00:00:00')
        );
    }

    /**
     *
     */
    function test_valid()
    {
        foreach ($this->valid_mappings as $input => $expect) {
            $this->assertEquals($expect, $this->field->validate($input));
        }

    }

    function test_invalid()
    {
        foreach ($this->invalid_mappings as $input => $expect) {
            try{
                $this->field->validate($input);
            }catch (\serializer\ValidationError $e){

                $this->assertEquals($expect, $e->getMessage());
            }
        }
    }
}


class FloatFieldTest extends PHPUnit_Framework_TestCase
{
    protected $field = null;
    protected $valid_mappings = array();
    protected $invalid_mappings = array(
        '' => 'A valid float is required.',
        'a' => 'A valid float is required.'
    );

    function setUp()
    {
        $this->field = new FloatField();
        $this->valid_mappings = array(
            '1.7' => 1.7,
            '1.1' => 1.1
        );
    }

    function test_valid()
    {
        foreach ($this->valid_mappings as $input => $expect) {
            $this->assertEquals($expect, $this->field->validate($input));
        }

    }

    function test_default()
    {
        $a = new FloatField(array('default' => 0.1));
        $this->assertEquals(0.1, $a->validate());

    }

    /**
     * @expectedException serializer\SkipField
     */
    function test_skip_field()
    {
        $a = new FloatField();
        $a->validate();
    }

    function test_invalid()
    {
        foreach ($this->invalid_mappings as $input => $expect) {
            try{
                $this->field->validate($input);
            }catch (\serializer\ValidationError $e){

                $this->assertEquals($expect, $e->getMessage());
            }
        }
    }
}
