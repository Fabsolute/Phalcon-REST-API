<?php


namespace Fabs\Rest\Exceptions;

abstract class StatusCodeException extends RestException
{
    /** @var mixed */
    protected $error_details = null;

    /**
     * StatusCodeException constructor.
     * @param int $code
     * @param string $error_message
     * @param mixed $error_details
     */
    public function __construct($code, $error_message, $error_details)
    {
        parent::__construct($error_message, $code);
        $this->error_details = $error_details;
    }

    public function getErrorDetails()
    {
        return $this->error_details;
    }
}