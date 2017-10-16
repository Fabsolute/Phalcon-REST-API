<?php

namespace Fabs\Rest\Models;

use Fabs\Serialize\Validation\ValidationBase;

class QueryElement
{
    /** @var string */
    private $query_name = null;
    /** @var string */
    private $field_name = null;
    /** @var bool */
    private $is_exact = false;
    /** @var bool */
    private $is_required = false;
    /** @var mixed */
    private $value = null;
    /** @var string[] */
    private $allowed_special_characters = [];
    /** @var callable */
    private $filter = null;
    /** @var ValidationBase[] */
    private $validation_list = [];
    /** @var callable|null */
    private $validation_failed_callback = null;

    protected function __construct($query_name)
    {
        $this->query_name = $query_name;
    }

    /**
     * @param string $field_name
     * @return QueryElement
     */
    public function setFieldName($field_name)
    {
        $this->field_name = $field_name;
        return $this;
    }

    /**
     * @param bool $is_exact
     * @return QueryElement
     */
    public function setIsExact($is_exact = true)
    {
        $this->is_exact = $is_exact;
        return $this;
    }

    /**
     * @param bool $is_required
     * @return QueryElement
     */
    public function setIsRequired($is_required = true)
    {
        $this->is_required = $is_required;
        return $this;
    }

    /**
     * @param ValidationBase $validation
     * @return QueryElement
     */
    public function addValidation($validation)
    {
        $this->validation_list[] = $validation;
        return $this;
    }

    /**
     * @param callable $validation_failed_callback
     * @return QueryElement
     */
    public function setValidationFailedCallback($validation_failed_callback)
    {
        $this->validation_failed_callback = $validation_failed_callback;
        return $this;
    }

    /**
     * @param $value mixed
     * @return QueryElement
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getQueryName()
    {
        return $this->query_name;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        if ($this->field_name !== null) {
            return $this->field_name;
        }

        return $this->getQueryName();
    }

    /**
     * @return bool
     */
    public function getIsExact()
    {
        return $this->is_exact;
    }

    /**
     * @return bool
     */
    public function getIsRequired()
    {
        return $this->is_required;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return ValidationBase[]
     */
    public function getValidationList()
    {
        return $this->validation_list;
    }

    /**
     * @param ValidationBase $validation
     * @return boolean
     */
    public function fireValidationFailed($validation)
    {
        if (is_callable($this->validation_failed_callback)) {
            return call_user_func($this->validation_failed_callback, [$this, $validation]);
        }

        return true;
    }

    /**
     * @param string $query_name
     * @return QueryElement
     */
    public static function create($query_name)
    {
        $query_element = new static($query_name);
        return $query_element;
    }

    /**
     * @return string[]
     */
    public function getAllowedSpecialCharacters()
    {
        return $this->allowed_special_characters;
    }

    /**
     * @param string[] $allowed_special_characters
     * @return QueryElement
     */
    public function setAllowedSpecialCharacters($allowed_special_characters)
    {
        $this->allowed_special_characters = $allowed_special_characters;
        return $this;
    }

    /**
     * @return callable
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param callable $filter
     * @return QueryElement
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }
}