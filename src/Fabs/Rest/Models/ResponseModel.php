<?php


namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class ResponseModel extends SerializableObject
{

    /** @var string */
    public $status = null;
    /** @var array|SerializableObject */
    public $data = null;


    public function __construct()
    {
        parent::__construct();

        $this->addRenderIfNotNullCondition('data');
    }
}