<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class UnauthorizedException extends StatusCodeException
{
    /**
     * UnauthorizedException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(401, HttpStatusCodes::UNAUTHORIZED, $error_details);
    }
}
