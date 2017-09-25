<?php


namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class ErrorModel extends SerializableObject
{

    /** @var string */
    public $status = null;
    /** @var string */
    public $error_message = null;
    /** @var null|SerializableObject */
    public $error_details = null;


    public function __construct()
    {
        parent::__construct();

        $this->addRenderIfNotNullCondition('error_details');
    }
}