<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class ConflictException extends StatusCodeException
{
    /**
     * ConflictException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(409, HttpStatusCodes::CONFLICT, $error_details);
    }
}
