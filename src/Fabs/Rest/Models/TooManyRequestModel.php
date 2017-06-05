<?php

namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class TooManyRequestModel extends SerializableObject
{
    public $try_count = 0;
    public $try_start_time = 0;

    public static function getEmpty()
    {
        $too_many_request_model = new TooManyRequestModel();
        $too_many_request_model->try_start_time = time();
        return $too_many_request_model;
    }
}