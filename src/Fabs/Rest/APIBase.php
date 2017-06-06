<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 10:23
 */

namespace Fabs\Rest;


use Fabs\Rest\Constants\HttpHeaders;
use Fabs\Rest\Constants\HttpMethods;
use Fabs\Rest\Constants\PatchOperations;
use Fabs\Rest\Models\MapModel;
use Fabs\Rest\Services\PatchMethodHandler;
use Fabs\Rest\Services\ServiceBase;
use Phalcon\Mvc\Micro\Collection as MicroCollection;

abstract class APIBase extends ServiceBase
{
    /**
     * @var string[]
     */
    private $allowed_methods = [];
    /**
     * @var MicroCollection
     */
    protected $collection;
    /**
     * @var MapModel[]
     */
    protected $mapped_functions = [];

    /**
     * @var PatchMethodHandler
     */
    protected $patch_method_handler = null;

    public function __construct()
    {
        $this->patch_method_handler = new PatchMethodHandler();
        $this->patch_method_handler->addAllowedOperation(PatchOperations::ADD);
        $this->patch_method_handler->addAllowedOperation(PatchOperations::REMOVE);
        $this->patch_method_handler->addAllowedOperation(PatchOperations::REPLACE);

        $this->addAllowedMethod(HttpMethods::GET);
        $this->addAllowedMethod(HttpMethods::POST);
        $this->addAllowedMethod(HttpMethods::HEAD);
        $this->addAllowedMethod(HttpMethods::PUT);
        $this->addAllowedMethod(HttpMethods::PATCH);
        $this->addAllowedMethod(HttpMethods::DELETE);

        $this->map(HttpMethods::GET, '/', 'get');
        $this->map(HttpMethods::POST, '/', 'post');

        $this->map(HttpMethods::HEAD, '/{id}', 'head');
        $this->map(HttpMethods::PUT, '/{id}', 'put');
        $this->map(HttpMethods::PATCH, '/{id}', 'patch');
        $this->map(HttpMethods::DELETE, '/{id}', 'delete');

        $this->collection = new MicroCollection();
        $this->collection->setHandler($this);
        $this->collection->setPrefix($this->getPrefix());

        $this->application->before(function () {
            $pattern = $this->router->getMatchedRoute()->getPattern();
            if (strpos($pattern, $this->getPrefix()) === 0) {
                return $this->before();
            }
            return true;
        });

        $this->application->after(function () {
            $pattern = $this->router->getMatchedRoute()->getPattern();
            if (strpos($pattern, $this->getPrefix()) === 0) {
                $this->after();
            }
        });
    }

    /**
     * @return string
     */
    protected abstract function getPrefix();

    public function get()
    {
        $this->status_code_handler->methodNotAllowed();
    }

    public function post()
    {
        $this->status_code_handler->methodNotAllowed();
    }

    public function patch($id)
    {
        $this->status_code_handler->methodNotAllowed();
    }

    public function put($id)
    {
        $this->status_code_handler->methodNotAllowed();
    }

    public function delete($id)
    {
        $this->status_code_handler->methodNotAllowed();
    }

    public function head($id)
    {
        $this->status_code_handler->methodNotAllowed();
    }

    protected function map($method, $url, $function_name)
    {
        $map = new MapModel();
        $map->method_name = $method;
        $map->url = $url;
        $map->function_name = $function_name;

        $this->mapped_functions[] = $map;
    }

    public function mount()
    {
        foreach ($this->mapped_functions as $map) {
            if (method_exists($this->collection, strtolower($map->method_name))) {
                if (in_array($map->method_name, $this->allowed_methods, true)) {
                    call_user_func_array([$this->collection, strtolower($map->method_name)],
                        [$map->url, $map->function_name]);
                } else {
                    call_user_func_array([$this->collection, strtolower($map->method_name)],
                        [$map->url, 'methodNotAllowed']);
                }
            }
        }

        $this->application->mount($this->collection);
    }

    protected function before()
    {
        $this->application->response->setHeader(HttpHeaders::ACCESS_CONTROL_ALLOW_METHODS,
            strtoupper(implode(', ', $this->allowed_methods)));
        return true;
    }

    protected function after()
    {

    }

    public function getETag()
    {
        return $this->request->getHeader(HttpHeaders::IF_NONE_MATCH);
    }

    protected function getPage()
    {
        return $this->request->getQuery('page', 'int', 0);
    }

    protected function getPerPage()
    {
        return $per_page = $this->request->getQuery('per_page', 'int', 0);
    }

    /**
     * @param $cache_key string
     * @return string
     */
    protected function makeCacheKey($cache_key)
    {
        return $cache_key . '_' . $this->getPage() . '_' . $this->getPerPage();
    }

    /**
     * @param string $method
     * @return APIBase
     */
    protected function addAllowedMethod($method)
    {
        $this->allowed_methods[$method] = $method;
        return $this;
    }

    /**
     * @param string $method
     * @return APIBase
     */
    protected function removeAllowedMethod($method)
    {
        unset($this->allowed_methods[$method]);
        return $this;
    }

    public function methodNotAllowed()
    {
        $this->status_code_handler->methodNotAllowed();
    }
}