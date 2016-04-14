<?php
/**
 * Created by PhpStorm.
 * User: letuananh
 * Date: 6/12/15
 * Time: 17:53
 */
namespace serializer;

class ModelSerializer extends Serializer
{
    public static $model = '';
    protected $modelEntity;

    public function __construct($instance = null, $data = null, $arg = array())
    {
        parent::__construct($instance, $data, $arg);
        $this->init_model();
    }

    /**
     *
     */
    private function init_model()
    {
    }


    /**
     * @param $validated_data
     * @return BasicObject|void
     */
    public function create($validated_data)
    {

    }

    /**
     * @param array $validated_data
     * @return mixed|void
     */
    public function update($instance, $validated_data)
    {
    }
}