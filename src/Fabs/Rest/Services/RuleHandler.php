<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 21/07/2017
 * Time: 10:35
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\Rules\RuleBase;

class RuleHandler extends ServiceBase
{
    /** @var string[] */
    protected $rule_class_list = [];
    /** @var RuleBase[] */
    protected $rule_instantiate_list = [];

    /** @var string[] */
    protected $rule_list = [];
    /** @var mixed */
    protected $context = null;

    /**
     * @param string $rule_name
     * @param string $rule_class_name
     * @return RuleHandler
     */
    public function setRule($rule_name, $rule_class_name)
    {
        $this->rule_class_list[$rule_name] = $rule_class_name;
        return $this;
    }

    /**
     * @param string $rule_name
     * @return RuleHandler
     */
    public function addRule($rule_name)
    {
        if (array_key_exists($rule_name, $this->rule_class_list) === true) {
            if (in_array($rule_name, $this->rule_list, true) === false) {
                $this->rule_list[] = $rule_name;
            }
        }

        return $this;
    }

    /**
     * @param mixed $context
     * @return RuleHandler
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @param string $rule_name
     * @return bool
     */
    public function executeRule($rule_name)
    {
        if (array_key_exists($rule_name, $this->rule_class_list) === true) {
            if (array_key_exists($rule_name, $this->rule_instantiate_list) === false) {
                $this->rule_instantiate_list[$rule_name] = new $this->rule_class_list[$rule_name];
            }

            $rule = $this->rule_instantiate_list[$rule_name];
            return $rule->execute($this->context);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function execute()
    {
        foreach ($this->rule_list as $rule_name) {
            $response = $this->executeRule($rule_name);
            if ($response === false) {
                return false;
            }
        }

        return true;
    }
}