<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class TooManyRequestException extends StatusCodeException
{
    /**
     * TooManyRequestException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(429, HttpStatusCodes::TOO_MANY_REQUEST, $error_details);
    }
}
