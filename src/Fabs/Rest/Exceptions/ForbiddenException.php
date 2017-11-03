<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class ForbiddenException extends StatusCodeException
{
    /**
     * ForbiddenException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(403, HttpStatusCodes::FORBIDDEN, $error_details);
    }
}
