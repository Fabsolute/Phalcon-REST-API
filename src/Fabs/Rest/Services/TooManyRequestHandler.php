<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 11:28
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\Constants\HttpHeaders;
use Fabs\Rest\Models\TooManyRequestModel;

/**
 * Class TooManyRequestHandler
 * @package Fabs\Rest\Services
 */
class TooManyRequestHandler extends ServiceBase
{
    /**
     * @var string
     */
    protected $prefix = '';
    /**
     * @var string
     */
    protected $suffix = '';

    protected $limit = 10;

    protected $time_period = 30;

    protected $disabled = false;

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function setTimePeriod($period_in_seconds)
    {
        $this->time_period = $period_in_seconds;
        return $this;
    }

    public function setDisable($disabled)
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isLimitReached()
    {
        if ($this->disabled) {
            return false;
        }

        $too_many_request_object = $this->getTooManyRequestObject();
        return $too_many_request_object->try_count >= $this->limit;
    }

    /**
     * @return TooManyRequestHandler
     */
    public function increaseRequestCount()
    {
        if ($this->disabled) {
            return $this;
        }
        $too_many_request_object = $this->getTooManyRequestObject();
        $too_many_request_object->try_count++;
        $this->cache->save($this->getCacheKey(), json_encode($too_many_request_object));
        return $this;
    }

    /**
     * @return TooManyRequestHandler
     */
    public function setHeaders()
    {
        if ($this->disabled) {
            return $this;
        }
        $too_many_request_object = $this->getTooManyRequestObject();
        $this->application->response->setHeader(HttpHeaders::X_RATELIMIT_LIMIT, $this->limit);
        $this->application->response->setHeader(HttpHeaders::X_RATELIMIT_REMAINING,
            max(0, $this->limit - $too_many_request_object->try_count));
        $this->application->response->setHeader(HttpHeaders::X_RATELIMIT_RESET,
            max(0, $too_many_request_object->try_start_time + $this->time_period - time()));
        return $this;
    }

    /**
     * @return TooManyRequestModel
     */
    private function getTooManyRequestObject()
    {
        $empty_too_many_request_object = TooManyRequestModel::getEmpty();
        $too_many_request_object_raw = json_decode($this->cache->get($this->getCacheKey()), true);
        $too_many_request_object = TooManyRequestModel::deserialize($too_many_request_object_raw);
        if ($too_many_request_object == null) {
            $too_many_request_object = $empty_too_many_request_object;
        }

        if ($too_many_request_object->try_start_time + $this->time_period < time()) {
            $too_many_request_object = $empty_too_many_request_object;
        }
        return $too_many_request_object;
    }

    protected function getCacheKey()
    {
        return 'too_many_request_' . $this->prefix . '_' . $this->suffix;
    }
}