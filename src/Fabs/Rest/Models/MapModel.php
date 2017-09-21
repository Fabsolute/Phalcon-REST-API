<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 13:35
 */

namespace Fabs\Rest\Models;

use Fabs\Serialize\SerializableObject;

class MapModel extends SerializableObject
{
    public $url = null;
    public $function_name = null;
    public $method_name = null;
    /** @var callable */
    public $before_callable = null;
    /** @var callable */
    public $after_callable = null;
    /** @var string[] */
    public $rule_list = [];

    /**
     * @param callable $before_callable
     * @return MapModel $this
     */
    public function setBeforeAction($before_callable)
    {
        $this->before_callable = $before_callable;
        return $this;
    }

    /**
     * @param callable $after_callable
     * @return MapModel $this
     */
    public function setAfterAction($after_callable)
    {
        $this->after_callable = $after_callable;
        return $this;
    }

    /**
     * @param string $rule
     * @return MapModel
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function addRule($rule)
    {
        $this->rule_list[] = $rule;
        return $this;
    }
}