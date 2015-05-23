<?php
/**
 * Created by PhpStorm.
 * User: Le
 * Date: 4/25/2015
 * Time: 2:38 AM
 */
namespace serializer;

/**
 * Class BasicObject
 * @package serializer
 * Basic object represent an instance
 */
class BasicObject{
    protected $_value = array();
    function __construct($_init_data = array()){
        $this->_value = $_init_data;
    }

    function __get($name){
        if(array_key_exists($name, $this->_value))
            return $this->_value[$name];
    }
    function __set($name, $value)
    {
        $this->_value[$name] = $value;
    }
}

class EmptyObject{

}

trait utils{

    /**
     * @param array $dictionary
     * @param array $keys
     * @param array $value
     *
     *
     * set_value(['a' => 1], [], ['b'=> 2]) -> ['a'=> 1, 'b'=> 2]
     * set_value(['a' => 1], ['x'], 2)      -> ['a'=> 1, 'x'=> 2}
     * set_value(['a' => 1], ['x', 'y'], 2) -> ['a'1, 'x'=> ['y'=> 2]]
     * if source is person.name.first_name = 'hello'
     * source_attrs = instance, attrs
     * => 'person' => ['name' => ['first_name' => 'hello']]
     *
     */
    public function set_value(&$dictionary, $keys, $value){
        if(count($keys) == 0){
            $dictionary = array_merge($dictionary , $value);
        }
        $new_value = $this->append_array(array(),$keys,$value);
        $dictionary = array_merge($dictionary, $new_value) ;
    }

    private function append_array($current_array, $value_array, $value){
        $current_key = array_shift($value_array);
        if(count($value_array) == 0)
            $current_array = array($current_key => $value);
        else
            $current_array[$current_key] = $this->append_array(array(),$value_array, $value);
        return $current_array;
    }


    /**
     * @param $instance
     * @param Array $attrs
     * $attr = [instance, attrs]
     *
     */
    public function get_attr($instance, $attrs){

        foreach($attrs as $attr){
            $instance = $instance[$attr];
        }
        return $instance;
    }

    /**
     *Used to support list values in HTML forms.
     *Supports lists of primitives and/or dictionaries.
     * List of primitives.
     *   {
     *       '[0]': 'abc',
     *       '[1]': 'def',
     *       '[2]': 'hij'
     *   }
     *    -->
     *   [
     *       'abc',
     *       'def',
     *       'hij'
     *   ]
     * List of dictionaries.
     * {
     *   '[0]foo': 'abc',
     *   '[0]bar': 'def',
     *   '[1]foo': 'hij',
     *   '[2]bar': 'klm',
     * }
     *   -->
     *[
     *   {'foo': 'abc', 'bar': 'def'},
     *   {'foo': 'hij', 'bar': 'klm'}
     *]
     * @param $dictionary
     * @param $prefix
     */
    public function parse_html_list($dictionary, $prefix){
        

    }
}