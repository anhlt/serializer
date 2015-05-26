<?php
namespace serializer;


class SkipField extends \Exception
{

}

class ValidationError extends \Exception
{

}

/**
 * @property array source_attrs
 */
class Field
{
    use utils;
    const _NOT_READ_ONLY_WRITE_ONLY = 'May not set both `read_only` and `write_only`';
    const _NOT_READ_ONLY_REQUIRED = 'May not set both `read_only` and `required`';
    const _NOT_READ_ONLY_DEFAULT = 'May not set both `read_only` and `default`';
    const _NOT_REQUIRED_DEFAULT = 'May not set both `required` and `default`';
    const _MISSING_ERROR_MESSAGE = 'ValidationError raised by `%s`, but error key `%s` does not exist in the `MESSAGES` dictionary.';
    public static $creation_counter = 0;
    public $field_name;
    public $parent;
    public $root;
    public $message = [
        'required' => 'This field is required.'
    ];

    public function __construct($arg = array())
    {
        $defaults = array(
            'read_only' => false,
            'write_only' => false,
            'require' => false,
            'default' => null,
            'initial' => null,
            'source' => null,
            'label' => null,
            'style' => null
        );
        $defaults = array_merge($defaults, $arg);
        self::$creation_counter = Field::$creation_counter;
        Field::$creation_counter += 1;

        assert(!($defaults['read_only'] && $defaults['write_only']), self::_NOT_READ_ONLY_WRITE_ONLY);
        assert(!($defaults['read_only'] && $defaults['require']), self::_NOT_READ_ONLY_REQUIRED);
        assert(!($defaults['read_only'] && !is_null($defaults['default'])), self::_NOT_READ_ONLY_DEFAULT);
        assert(!($defaults['require'] && !is_null($defaults['default'])), self::_NOT_REQUIRED_DEFAULT);

        $this->read_only = $defaults['read_only'];
        $this->write_only = $defaults['write_only'];
        $this->require = $defaults['require'];
        $this->default = $defaults['default'];
        $this->initial = $defaults['initial'];
        $this->source = $defaults['source'];
        $this->label = $defaults['label'];
        if (is_null($defaults['style'])) {
            $this->style = array();
        } else {
            $this->style = $defaults['style'];
        }
    }

    /**
     * @param $field_name
     * @param $parent
     * @param $root
     */
    public function bind($field_name, $parent, $root)
    {
        # Setup the context for field name instance
        $this->field_name = $field_name;
        $this->parent = $parent;
        $this->root = $root;

        # $this->label should be base on the field name
        if (is_null($this->label)) {
            $this->label = ucfirst(str_replace('_', ' ', $field_name));
        }

        # $this->source should default to being the same as field name
        if (is_null($this->source)) {
            $this->source = $field_name;
        }
        # TODO: Findout about source
        if ($this->source == '*') {
            $this->source_attrs = array();
        } else {
            $this->source_attrs = explode('.', $this->source);
        }
    }

    public function get_initial()
    {
        # Return a value to use when the field is being returned as a primative
        # value, without any object instance.

        return $this->initial;
    }

    public function get_value($obj)
    {
        # Given the *incoming* primative data, return the value for this field
        # that should be validated and transformed to a native value.
        if (array_key_exists($this->field_name, $obj))
            return $obj[$this->field_name];
        else
            return null;
    }

    public function get_attribute($instance)
    {
        # Given the *outgoing* object instance, return the value for this field
        # that should be returned as a primative value.
        return $this->get_attr($instance, $this->source_attrs);
    }

    public function get_default()
    {
        #     Return the default value to use when validating data if no input
        #     is provided for this field.
        #     If a default has not been set for this field then this will simply
        #     return `empty`, indicating that no value should be set in the
        #     validated data for this field.
        if (is_null($this->default)) {
            throw new SkipField();
        }

        return $this->default;
    }

    public function validate($data = null)
    {
        #        Validate a simple representation and return the internal value.
        #        The provided data may be `empty` if no representation was included.
        #        May return `empty` if the field should not be included in the
        #        validated data.

        if (is_null($data)) {
            if ($this->require) {
                $this->fail('required');
            }

            return $this->get_default();
        }

        return $this->to_native($data);
    }

    public function to_native($data)
    {
        return $data;
    }

    public function to_primative($value)
    {
        return $value;
    }

    public function fail($key)
    {
        if (array_key_exists($key, $this->message)) {
            throw new ValidationError($this->message[$key]);
        }
    }

}

class BooleanField extends Field
{
    public $message = [
        'required' => 'This field is required.',
        'invalid_value' => ' input is not a valid boolean.'
    ];

    public $TRUE_VALUES = ['t', 'T', 'true', 'True', 'TRUE', '1', 1, true];
    public $FALSE_VALUES = ['f', 'F', 'false', 'False', 'FALSE', '0', 0, 0.0, false];

    public function get_value($obj)
    {
        return $obj[$this->field_name];
    }

    public function to_native($data)
    {
        if (in_array($data, $this->TRUE_VALUES)) {
            return true;
        } elseif (in_array($data, $this->FALSE_VALUES)) {
            return false;
        } else {
            $this->fail('invalid_value');
        }
    }
}

class CharField extends Field
{
    public $message = [
        'required' => 'This field is required.',
        'blank' => 'This field may not be black'
    ];
    protected $allow_blank = false;

    public function __construct($arg = array('allow_blank' => false))
    {
        $this->allow_blank = $arg['allow_blank'];
        parent::__construct($arg);
    }
}


class IntegerField extends Field
{
    public $message = [
        'required' => 'This field is required.',
        'invalid_integer' => 'A valid integer is required.'
    ];

    public function to_native($data)
    {
        if (is_numeric($data)) {
            return (int)$data;
        }
        $this->fail('invalid_integer');
    }
}

class ChoiceField extends Field
{
    public $message = [
        'required' => 'This field is required.',
        'invalid_choice' => 'This is not a valid choice'
    ];

    public function __construct($arg)
    {
        if(array_key_exists('choices', $arg)){
            $choices = $arg['choices'];
            unset($arg['choices']);
        }
        assert($choices, '`choices` arguments is required and may not be empty');
        
    }

}

?>
