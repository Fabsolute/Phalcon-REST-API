<?php

namespace Fabs\Rest\Models;

class SearchQueryModel
{
    /** @var QueryElement[] */
    private $query_element_list = [];
    /** @var SortQueryElement */
    private $sort_query_element = null;

    public function __construct($query_element_list, $sort_query_element)
    {
        $this->query_element_list = $query_element_list;
        $this->sort_query_element = $sort_query_element;
    }

    /**
     * @return SortQueryElement|null
     */
    public function getSortQueryElement()
    {
        return $this->sort_query_element;
    }

    /**
     * @return QueryElement[]
     */
    public function getQueryElementList()
    {
        return $this->query_element_list;
    }
}
