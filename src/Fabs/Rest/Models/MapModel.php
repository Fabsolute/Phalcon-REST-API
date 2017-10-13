<?php

namespace Fabs\Rest\Models;

use Fabs\Rest\Services\ServiceBase;

class MapModel extends ServiceBase
{
    /** @var string */
    private $uri = null;
    /** @var string */
    private $function_name = null;
    /** @var string */
    private $method_name = null;

    /** @var callable */
    private $before_callable = null;
    /** @var callable */
    private $after_callable = null;
    /** @var string[] */
    private $rule_list = [];
    /** @var QueryElement[] */
    private $query_list = [];
    /** @var SortQueryElement */
    private $default_sort_query_element = null;

    public function __construct($method_name, $uri, $function_name)
    {
        $this->method_name = strtolower($method_name);
        $this->uri = $uri;
        $this->function_name = $function_name;
    }

    /**
     * @return string
     */
    public function getURI()
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getFunctionName()
    {
        return $this->function_name;
    }

    /**
     * @return string
     */
    public function getMethodName()
    {
        return $this->method_name;
    }

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
     */
    public function addRule($rule)
    {
        $this->rule_list[] = $rule;
        return $this;
    }

    /**
     * @param QueryElement $query_element
     * @return MapModel
     */
    public function addQueryElement($query_element)
    {
        $this->query_list[] = $query_element;
        return $this;
    }

    /**
     * @return callable
     */
    public function getBeforeAction()
    {
        return $this->before_callable;
    }

    /**
     * @return callable
     */
    public function getAfterAction()
    {
        return $this->after_callable;
    }

    /**
     * @return string[]
     */
    public function getRuleList()
    {
        return $this->rule_list;
    }

    /**
     * @return QueryElement[]
     */
    public function getQueryElementList()
    {
        return $this->query_list;
    }

    /**
     * @return SortQueryElement
     */
    public function getDefaultSortQueryElement()
    {
        return $this->default_sort_query_element;
    }

    /**
     * @param SortQueryElement $default_sort_query_element
     * @return MapModel
     */
    public function setDefaultSortQueryElement($default_sort_query_element)
    {
        $this->default_sort_query_element = $default_sort_query_element;
        return $this;
    }

    public function executeBefore()
    {
        foreach ($this->getRuleList() as $rule_name) {
            $this->rule_handler->addRule($rule_name);
        }

        $user_func = $this->getBeforeAction();
        if (is_callable($user_func)) {
            $response = call_user_func($user_func);
            if ($response !== true) {
                return false;
            }
        }

        $query_element_list = [];
        $sort_by = $this->request->getQuery('sort_by');
        $sort_by_descending = $this->request->getQuery('sort_by_descending');
        $sort_query_element = null;
        foreach ($this->getQueryElementList() as $query_element) {
            $query_data = $this->request->getQuery($query_element->getQueryName());
            if ($query_data !== null) {
                if (is_callable($query_element->getFilter())) {
                    $query_data = call_user_func($query_element->getFilter(), $query_data);
                }
                $query_element_list[] = $query_element->setValue($query_data);
            }

            if ($sort_by !== null || $sort_by_descending !== null) {
                if ($query_element instanceof SortQueryElement) {
                    $sort_name = $sort_by ?? $sort_by_descending;
                    if ($query_element->getQueryName() === $sort_name) {
                        $sort_query_element = $query_element;
                        if ($sort_by === null) {
                            $sort_query_element->setDescending(true);
                        }
                    }
                }
            }
        }

        if (count($query_element_list) > 0) {
            if ($sort_query_element === null) {
                $sort_query_element = $this->default_sort_query_element;
            }

            $search_queries = new SearchQueryModel($query_element_list, $sort_query_element);
            $this->dispatcher->setParam('search_query', $search_queries);
        }

        return true;
    }

    public function executeAfter()
    {
        $user_func = $this->getAfterAction();
        if (is_callable($user_func)) {
            call_user_func($user_func);
        }
    }
}