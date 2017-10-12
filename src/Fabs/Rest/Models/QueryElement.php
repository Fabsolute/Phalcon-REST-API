<?php

namespace Fabs\Rest\Models;

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
     * @param string $query_name
     * @return QueryElement
     */
    public static function create($query_name)
    {
        $query_element = new static($query_name);
        return $query_element;
    }

    /**
     * @param string $query_name
     * @return SortQueryElement
     */
    public static function createSortable($query_name)
    {
        $query_element = new SortQueryElement($query_name);
        return $query_element;
    }
}