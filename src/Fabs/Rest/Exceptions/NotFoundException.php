<?php

namespace Fabs\Rest\Exceptions;

use Fabs\Rest\Constants\HttpStatusCodes;

class NotFoundException extends StatusCodeException
{
    /**
     * NotFoundException constructor.
     * @param mixed $error_details
     */
    public function __construct($error_details = null)
    {
        parent::__construct(404, HttpStatusCodes::NOT_FOUND, $error_details);
    }
}
