<?php
/**
 * Created by PhpStorm.
 * User: letuananh
 * Date: 4/20/15
 * Time: 19:13
 */

namespace serializer;

abstract class BaseSerializer extends Field
{
    protected $instance;
    private $init_data;
    protected $_validated_data;
    private $_error = null;
    private $_data = null;

    public function __construct($instance = null, $data = null, $arg = array())
    {
        parent::__construct($arg);
        $this->instance = $instance;
        $this->init_data = $data;
    }

    public function to_native($data)
    {
        throw new \Exception('NotImplementedError');
    }

    public function to_primative($instance)
    {
        throw new \Exception('NotImplementedError');
    }

    public function save()
    {
        throw new \Exception('NotImplementedError');
    }

    public function is_valid()
    {
        try {
            $this->_validated_data = $this->to_native($this->init_data);
        } catch (\Exception $e) {
            // TODO: validate
            $this->_validated_data = [];
            $this->_error = $e->getMessage();

            return false;
        }
        $this->_error = false;

        return true;
    }

    function __get($name)
    {
        if ($name == 'data') {
            if (is_null($this->_data)) {
                if (!is_null($this->instance)) {
                    $this->_data = $this->to_primative($this->instance);
                } elseif (!is_null($this->init_data)) {
                    # generate data from
                    $this->_data = [];
                    foreach ($this->fields as $field_name => $field) {
                        $this->_data[ $field_name ] = $field;
                    }
                } else {
                    $this->_data = $this->get_initial();
                }
            }

            return $this->_data;
        }

        if ($name = 'errors') {
            if (!is_null($this->_error)) {
                $msg = 'You must call `is_valid()` before accessing `.errors`.';
                throw new \Exception($msg);
            }

            return $this->_error;
        }

        if ($name = 'validated_data') {
            if (!is_null($this->_validated_data)) {
                $msg = 'You must call `.is_valid()` before accessing `.errors`.';
                throw new \Exception($msg);
            }

            return $this->_validated_data;
        }

        return null;
    }
}

class Serializer extends BaseSerializer
{
    protected $fields = array();

    public function __construct($instance = null, $data = null, $arg = array())
    {
        parent::__construct($instance, $data, $arg);
        # Generate field_list
        $this->define_fields();
        foreach ($this->fields as $field_name => $field) {
            $field->bind($field_name, $this, $this);
        }

    }

    # overwrite in child class
    public function define_fields()
    {
        # must be overwrite like this way
        # $this->fields['name'] = new CharField(array('allow_blank' => false));
        throw new \Exception('NotImplementedError');
    }

    public function get_initial()
    {
        $data = [];
        foreach ($this->fields as $field_name => $field) {
            $data[ $field_name ] = $field->get_initial();
        }
    }

    public function get_value($obj)
    {
        return $obj[ $this->field_name ];
    }

    /**
     * @param $data
     * @return array
     * @throws ValidationError
     */
    public function to_native($data)
    {
        $ret = array();
        $error = array();
        foreach ($this->fields as $field) {
            if($field->read_only)
                continue;
            $primitive_value = $field . get_value($data);

            try {
                $validated_value = $field . validate($primitive_value);
            } catch (\Exception $e) {
                $error[ $field->field_name ] = $e->getMessage();
            } catch (SkipField $e) {

            }
            $ret[ $field->source_attrs ] = $validated_value;
        }

        if ($error) {
            throw new ValidationError($error);
        }

        return $ret;
    }

    /**
     * @param $instance
     * @return array
     */
    public function to_primative($instance)
    {
        $ret = array();
        foreach($this->fields as $field){
            if($field->read_only)
                continue;
            $native_value = $field->get_attribute($instance);
            $ret[$field->field_name] = $field->to_primative($native_value);
        }

        return $ret;
    }

    public function update($instance, $validated_data){
        # TODO: Update and return $instance

        return $instance;
    }

    public function create($validated_data){
        return array();
    }

    public function save()
    {
        if(!is_null($this->instance)){

        }
        $this->instance = $this->create($this->validated_data);
    }
}