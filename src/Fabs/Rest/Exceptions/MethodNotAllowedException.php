<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class MethodNotAllowedException extends StatusCodeException
{
    /**
     * MethodNotAllowedException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(405, HttpStatusCodes::METHOD_NOT_ALLOWED, $error_details);
    }
}
