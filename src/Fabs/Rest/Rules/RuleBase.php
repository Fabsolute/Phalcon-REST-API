<?php

namespace Fabs\Rest\Rules;

use Fabs\Rest\Services\ServiceBase;

abstract class RuleBase extends ServiceBase
{
    /**
     * @param mixed $context
     * @return bool
     */
    public abstract function execute($context);
}
