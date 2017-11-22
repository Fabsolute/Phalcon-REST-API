<?php


namespace Fabs\Rest\Conditions;

use Fabs\Serialize\Condition\ConditionBase;

class RenderIfNotValue extends ConditionBase
{
    private $value = INF;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function apply($value)
    {
        if ($value === $this->value) {
            $this->should_render = false;
        } else {
            $this->should_render = true;
        }
    }
}