<?php


namespace Fabs\Rest\Models;


class SortQueryElement extends QueryElement
{
    /** @var bool */
    private $descending = false;
    /** @var string */
    private $type = null;

    /**
     * @param bool $descending
     * @return SortQueryElement
     */
    public function setDescending($descending = true)
    {
        $this->descending = $descending;
        return $this;
    }

    /**
     * @param string $type
     * @return SortQueryElement
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDescending()
    {
        return $this->descending;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $query_name
     * @return SortQueryElement
     */
    public static function create($query_name)
    {
        /** @var SortQueryElement $query_element */
        $query_element = parent::create($query_name);
        return $query_element;
    }
}