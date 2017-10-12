<?php


namespace Fabs\Rest\Models;


class SortQueryElement extends QueryElement
{
    /** @var bool */
    private $is_descending = false;
    /** @var string */
    private $type = null;

    /**
     * @param bool $is_descending
     * @return SortQueryElement
     */
    public function setIsDescending($is_descending = true)
    {
        $this->is_descending = $is_descending;
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
}