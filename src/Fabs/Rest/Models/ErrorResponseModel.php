<?php


namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class ErrorResponseModel extends ResponseModel
{

    /** @deprecated
     *  @var array|null
     */
    public $error_list = null;

    /** @var string */
    public $error_message = null;
    /** @var null|SerializableObject */
    public $error_details = null;


    public function __construct()
    {
        parent::__construct();

        $this->addRenderIfNotNullCondition('error_list');
        $this->addRenderIfNotNullCondition('error_message');
        $this->addRenderIfNotNullCondition('error_details');
    }
}