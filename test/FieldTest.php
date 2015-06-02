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
        $this->field = new DateTimeField();
        $this->valid_mappings = array(
            '1975-04-30' => Carbon::createFromFormat('Y-m-d H:i:s', '1975-04-30 00:00:00')
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
}
