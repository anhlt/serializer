<?php
/**
 * Created by PhpStorm.
 * User: Le
 * Date: 4/25/2015
 * Time: 2:38 AM
 */
namespace serializer;
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
     * source_attrs = ['person','name','first_name']
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
}