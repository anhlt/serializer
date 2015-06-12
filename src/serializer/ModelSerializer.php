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
        $StoreFactory = new aafwEntityStoreFactory ();
        $this->modelEntity = $StoreFactory->create($this::$model);
    }


    /**
     * @param $validated_data
     * @return BasicObject|void
     */
    public function create($validated_data)
    {
        $instance = $this->modelEntity->createEmptyObject();
        foreach ($validated_data as $key => $value) {
            $instance->$key = $value;
        }
        $this->modelEntity->save($instance);

    }

    /**
     * @param aafwEntityStoreBase $instance
     * @param array $validated_data
     * @return mixed|void
     */
    public function update($instance, $validated_data)
    {
        foreach ($validated_data as $key => $value) {
            $instance->$key = $value;
        }
        $this->modelEntity->save($instance);
    }
}