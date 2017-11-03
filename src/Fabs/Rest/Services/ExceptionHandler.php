<?php


namespace Fabs\Rest\Services;


class ExceptionHandler extends ServiceBase
{
    public function handle($exception)
    {
        throw $exception;
    }
}
