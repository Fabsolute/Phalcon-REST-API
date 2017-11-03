<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class BadRequestException extends StatusCodeException
{
    /**
     * BadRequestException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(400, HttpStatusCodes::BAD_REQUEST, $error_details);
    }
}
