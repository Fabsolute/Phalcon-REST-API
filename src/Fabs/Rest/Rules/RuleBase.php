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

    /**
     * @return null
     * @todo it will be abstract ^v2
     */
    public function getName(){
        return null;
    }
}
