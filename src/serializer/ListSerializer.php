<?php
/**
 * Created by PhpStorm.
 * User: letuananh
 * Date: 5/26/15
 * Time: 15:47
 */

namespace serializer;


/**
 * Class ListSerializer
 * @package serializer
 */
class ListSerializer extends Serializer
{
    /**
     * @var \serializer\Field null
     */
    protected $child = null;
    /**
     * @var array
     */
    protected $initial = array();

    /**
     * @param null $instance
     * @param null $data
     * @param array $arg
     */
    public function __construct($instance = null, $data = null, $arg = array())
    {
        $this->define_fields();
        $this->child = $this->child or $arg[ 'child' ];
        assert(!is_null($this->child), '`child` is a requirement argument');
        parent::__construct($instance, $data, $arg);
        $this->child->bind('', $this, $this);
    }

    public function bind($field_name, $parent, $root)
    {
        parent::bind($field_name, $parent, $root);
        $this->child->bind($field_name, $this, $root);
    }

    /**
     * @param $dict
     * @return mixed
     */
    public function get_value($dict)
    {
        return $dict[ $this->field_name ];
    }


    public function to_native($data){

        $native_data = array();

        // Strategy
        if( $data instanceof HTMLDict){
            $data = $this->parse_html_list($data->as_array());
        }

        foreach($data as $child_data){
            $native_data[] = $this->child->validate($child_data);
        }

        return $native_data;
    }
    /**
     *
     */
    public function to_primative($data)
    {
        $primative_data = array();
        foreach ($data as $child) {
            $primative_data[ ] = $this->child->to_primative($child);
        }
        return $primative_data;
    }

    public function create($attrs_list)
    {
        $list_objects = array();
        foreach ($attrs_list as $attrs) {
            $list_objects[ ] = new BasicObject($attrs);
        }

        return $list_objects;
    }

    public function save()
    {
        if (!is_null($this->instance)) {
            $this->update($this->instance, $this->validated_data);
        }
        $this->instance = $this->create($this->validated_data);

        return $this->instance;
    }

    public function define_fields()
    {
        # must be overwrite like this way
        # $this->child = new CharField(array('allow_blank' => false));
        throw new \Exception('NotImplementedError');
    }

}