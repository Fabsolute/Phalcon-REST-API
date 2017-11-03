<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class UnsupportedMediaTypeException extends StatusCodeException
{
    /**
     * UnsupportedMediaTypeException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(415, HttpStatusCodes::UNSUPPORTED_MEDIA_TYPE, $error_details);
    }
}
