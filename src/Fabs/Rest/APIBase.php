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
use Fabs\Rest\Exceptions\MethodNotAllowedException;
use Fabs\Rest\Exceptions\UnprocessableEntityException;
use Fabs\Rest\Models\MapModel;
use Fabs\Rest\Models\QueryElement;
use Fabs\Rest\Models\SearchQueryModel;
use Fabs\Rest\Services\PatchHandler;
use Fabs\Rest\Services\ServiceBase;
use Fabs\Serialize\SerializableObject;
use Fabs\Serialize\Validation\ValidationException;
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
     * @var PatchHandler
     */
    protected $patch_handler = null;

    public function __construct()
    {
        $this->patch_handler = new PatchHandler();
        $this->patch_handler->addAllowedOperation(PatchOperations::ADD);
        $this->patch_handler->addAllowedOperation(PatchOperations::REMOVE);
        $this->patch_handler->addAllowedOperation(PatchOperations::REPLACE);

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
    }

    /**
     * @return string
     */
    public abstract function getPrefix();

    public function get()
    {
        throw new MethodNotAllowedException();
    }

    public function post()
    {
        throw new MethodNotAllowedException();
    }

    public function patch($id)
    {
        throw new MethodNotAllowedException();
    }

    public function put($id)
    {
        throw new MethodNotAllowedException();
    }

    public function delete($id)
    {
        throw new MethodNotAllowedException();
    }

    public function head($id)
    {
        throw new MethodNotAllowedException();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $function_name
     * @return MapModel
     */
    protected function map($method, $uri, $function_name)
    {
        $map = new MapModel($this, $method, $uri, $function_name);

        foreach ($this->mapped_functions as $key => $map_model) {
            if ($map_model->getMethodName() == $method && $map_model->getURI() == $uri) {
                $this->mapped_functions[$key] = $map;
                return $map;
            }
        }

        $this->mapped_functions[] = $map;
        return $map;
    }

    public function mount()
    {
        foreach ($this->mapped_functions as $map) {
            if (method_exists($this->collection, $map->getMethodName())) {
                if (in_array(strtoupper($map->getMethodName()), $this->allowed_methods, true)) {
                    call_user_func_array(
                        [
                            $this->collection,
                            $map->getMethodName()
                        ],
                        [
                            $map->getURI(),
                            $map->getFunctionName(),
                            $this->getPrefix() . $map->getURI()
                        ]
                    );
                } else {
                    call_user_func_array(
                        [
                            $this->collection,
                            $map->getMethodName()
                        ],
                        [
                            $map->getURI(),
                            'methodNotAllowed'
                        ]
                    );
                }
            }
        }

        $this->application->mount($this->collection);
    }

    /**
     * @return Models\MapModel[]
     */
    public function getMappedFunctions()
    {
        return $this->mapped_functions;
    }

    public function awake()
    {

    }

    public function before()
    {
        $this->application->response->setHeader(
            HttpHeaders::ACCESS_CONTROL_ALLOW_METHODS,
            strtoupper(implode(', ', $this->allowed_methods))
        );
        return true;
    }

    public function after()
    {

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
        throw new MethodNotAllowedException();
    }

    /**
     * @return SearchQueryModel|null
     */
    public function getSearchQuery()
    {
        $search_query = $this->dispatcher->getParam('search_query');
        return $search_query;
    }

    /**
     * @param string $include_name
     * @return bool
     * @author ahmetturk <ahmetturk93@gmail.com>
     */
    public function hasInclude($include_name)
    {
        $include_name = trim(strtolower($include_name));
        /** @var string[] $include_list */
        $include_list = $this->dispatcher->getParam('include_list');
        if ($include_list === null || count($include_list) === 0) {
            return false;
        }

        return in_array($include_name, $include_list, true);
    }

    /**
     * @return SerializableObject|mixed
     */
    public function getRequestModel()
    {
        $request_model = $this->dispatcher->getParam('request_model');
        return $request_model;
    }

    /**
     * @param ValidationException $e
     * @return bool
     * @throws UnprocessableEntityException
     */
    public function onValidationException($e)
    {
        throw new UnprocessableEntityException(
            [
                'field' => $e->getPropertyName(),
                'type' => $e->getValidatorName()
            ]
        );
    }
}