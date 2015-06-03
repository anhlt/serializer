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
    protected $init_data;
    protected $_validated_data;
    protected $_error = null;
    protected $_data = null;
    protected $fields = array();


    public function __construct($instance = null, $data = null, $arg = array())
    {
        parent::__construct($arg);
        $this->instance = $instance;
        $this->init_data = $data;
    }

    public function save()
    {
        throw new \Exception('NotImplementedError');
    }

    /**
     * @return bool
     */
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

    public function to_native($data)
    {
        throw new \Exception('NotImplementedError');
    }

    /**
     * @param $name
     * @return array|null|void
     * @throws \Exception
     */
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

        if ($name == 'errors') {
            if (is_null($this->_error)) {
                $msg = 'You must call `is_valid()` before accessing `.errors`.';
                throw new \Exception($msg);
            }

            return $this->_error;
        }

        if ($name == 'validated_data') {
            if (is_null($this->_validated_data)) {
                $msg = 'You must call `.is_valid()` before accessing `.validated_data`.';
                throw new \Exception($msg);
            }

            return $this->_validated_data;
        }


        throw new \Exception('Access to undefined properties');
    }

    public function to_primative($instance)
    {
        throw new \Exception('NotImplementedError');
    }

}

/**
 * @property array validated_data
 */
class Serializer extends BaseSerializer
{
    use utils;


    /**
     * @param null $instance
     * @param null $data
     * @param array $arg
     * @throws \Exception
     */
    public function __construct($instance = null, $data = null, $arg = array())
    {
        parent::__construct($instance, $data, $arg);
        # Generate field_list
        $this->define_fields();
        /** @var \serializer\Field $field */
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

    /**
     *
     */
    public function get_initial()
    {
        $data = [];
        /** @var \serializer\Field $field */
        foreach ($this->fields as $field_name => $field) {
            $data[ $field_name ] = $field->get_initial();
        }
    }

    /**
     * @param $data
     * @return array
     * @throws ValidationError
     */
    public function to_native($data)
    {
        $ret = array();
        $errors = array();
        /** @var \serializer\Field $field */
        foreach ($this->fields as $field) {
            if ($field->read_only) {
                continue;
            }
            $primitive_value = $this->get_value($data);
            try {
                $validated_value = $field->validate($primitive_value);
            } catch (SkipField $e) {

            } catch (\Exception $e) {
                $errors[ $field->field_name ] = $e->getMessage();
            }
            $this->set_value($ret, $field->source_attrs, $validated_value);
        }

        if ($errors) {
            $message = '';
            foreach ($errors as $error_key => $message_string) {
                $message .= $message_string . '\n';
            }
            throw new ValidationError($message);
        }

        return $ret;
    }

    public function get_value($obj)
    {
        return $obj[ $this->field_name ];
    }

    /**
     * @param $instance
     * @return array
     */
    public function to_primative($instance)
    {
        $ret = array();
        /** @var \serializer\Field $field */
        foreach ($this->fields as $field) {
            if ($field->read_only) {
                continue;
            }
            if (method_exists($this, $field->field_name)) {
                $method_name = $field->field_name;
                $native_value = $this->$method_name($instance);
            } else {
                $native_value = $field->get_attribute($instance);
            }

            $ret[ $field->field_name ] = $field->to_primative($native_value);
        }


        return $ret;
    }

    public function save()
    {
        if (!is_null($this->instance)) {
            $this->update($this->instance, $this->validated_data);
        }
        $this->instance = $this->create($this->validated_data);

        return $this->instance;
    }

    /**
     * @param \serializer\BasicObject $instance
     * @param array $validated_data
     * @return mixed
     */
    public function update($instance, $validated_data)
    {
        foreach ($validated_data as $key => $value) {
            $instance->$key = $value;
        }

        return $instance;
    }

    /**
     * @param $validated_data
     * @return BasicObject
     */
    public function create($validated_data)
    {
        $obj = new BasicObject();
        /** @var Array $validated_data */
        foreach ($validated_data as $key => $value) {
            $obj->$key = $value;
        }

        return $obj;
    }
}

