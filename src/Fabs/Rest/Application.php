<?php

namespace Fabs\Rest;

use Fabs\Rest\Constants\HttpHeaders;
use Fabs\Rest\Constants\HttpMethods;
use Fabs\Rest\Services\AutoloadHandler;
use Fabs\Rest\Services\HttpStatusCodeHandler;
use Fabs\Rest\Services\PaginationHandler;
use Fabs\Rest\Services\TooManyRequestHandler;
use Phalcon\Cache\BackendInterface;
use Phalcon\Exception;

class Application extends BaseApplication
{
    /**
     * @var string[]
     */
    protected $request_data;
    /**
     * @var string[]
     */
    protected $expose_headers = [];

    /**
     * @var bool
     */
    protected $require_json = true;
    /**
     * @var bool
     */
    protected $require_body = true;

    /**
     * @var TooManyRequestHandler
     */
    public $ip_too_many_request_handler = null;

    public function __construct($di = null)
    {
        parent::__construct($di);

        $this->ip_too_many_request_handler = (new TooManyRequestHandler())->setPrefix('ip');
        $this->ip_too_many_request_handler->setSuffix($this->request->getClientAddress());
        $this->ip_too_many_request_handler->setDisable(true);

        $this->request->setHttpMethodParameterOverride(true);
    }

    public function handle($uri = null)
    {
        $this->checkDIRequirements();
        parent::handle($uri);
    }

    /**
     * @return array
     */
    public function getRequestData()
    {
        if ($this->request_data == null) {
            $this->request_data = $this->request->getJsonRawBody(true);
            if ($this->request_data === false || !is_array($this->request_data)) {
                $this->request_data = [];
            }
        }
        return $this->request_data;
    }

    /**
     * @param string $header
     * @return Application
     */
    public function addExposeHeader($header)
    {
        $this->expose_headers[$header] = $header;
        return $this;
    }

    /**
     * @param string $header
     * @return Application
     */
    public function removeExposeHeader($header)
    {
        unset($this->expose_headers[$header]);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExposeHeaders()
    {
        return $this->expose_headers;
    }

    public function onBefore()
    {
        $on_before_operations_result = $this->onBeforeOperations();
        if ($on_before_operations_result === false) {
            $this->stop();
        }

        return $on_before_operations_result;
    }

    public function onBeforeOperations()
    {
        $pattern = $this->router->getMatchedRoute()->getPattern();
        foreach ($this->autoload_handler->getAPIList() as $api) {
            if (strpos($pattern, $api->getPrefix()) === 0) {
                $api->awake();
                break;
            }
        }

        $method = $this->request->getMethod();

        $this->ip_too_many_request_handler->increaseRequestCount();
        $this->ip_too_many_request_handler->setHeaders();
        if ($this->ip_too_many_request_handler->isLimitReached()) {
            $this->status_code_handler->tooManyRequest();
            return false;
        }

        $data = null;
        $is_data_required = $method == HttpMethods::POST
            || $method == HttpMethods::PUT
            || $method == HttpMethods::PATCH;

        if ($is_data_required && $this->require_json === true) {
            $content_type = $this->request->getHeader(HttpHeaders::CONTENT_TYPE);
            if ($content_type != 'application/json') {
                $this->status_code_handler->unsupportedMediaType([
                    HttpHeaders::CONTENT_TYPE => $content_type
                ]);
                return false;
            }

            if ($this->require_body === true) {
                $data = $this->getRequestData();
                if (count($data) == 0) {
                    if (json_last_error() != JSON_ERROR_NONE) {
                        $this->status_code_handler->badRequest();
                        return false;
                    } else {
                        $this->status_code_handler->unprocessableEntity();
                        return false;
                    }
                }
            }
        }

        $parent_before_response = parent::onBefore();
        if ($parent_before_response) {
            $pattern = $this->router->getMatchedRoute()->getPattern();
            foreach ($this->autoload_handler->getAPIList() as $api) {
                if (strpos($pattern, $api->getPrefix()) === 0) {

                    $before_state = $api->before();
                    if ($before_state !== true) {
                        return false;
                    }

                    $name = $this->router->getMatchedRoute()->getName();
                    foreach ($api->getMappedFunctions() as $mapped_function) {
                        if ($mapped_function->getMethodName() === strtolower($this->request->getMethod())) {
                            $uri = $api->getPrefix() . $mapped_function->getURI();
                            if ($name === $uri) {
                                $response = $mapped_function->executeBefore();
                                if ($response !== true) {
                                    return false;
                                }
                            }
                        }
                    }

                    break;
                }
            }

            return $this->rule_handler->execute();
        }

        return false;
    }

    public function onAfter()
    {
        if (!$this->response->isSent()) {

            $pattern = $this->router->getMatchedRoute()->getPattern();
            foreach ($this->autoload_handler->getAPIList() as $api) {
                if (strpos($pattern, $api->getPrefix()) === 0) {
                    $name = $this->router->getMatchedRoute()->getName();
                    foreach ($api->getMappedFunctions() as $mapped_function) {
                        if ($mapped_function->getMethodName() === strtolower($this->request->getMethod())) {
                            $uri = $api->getPrefix() . $mapped_function->getURI();
                            if ($name === $uri) {
                                $mapped_function->executeAfter();
                                break;
                            }
                        }
                    }
                    $api->after();
                    break;
                }
            }

            $method = $this->request->getMethod();
            $content = $this->getReturnedValue();
            $is_not_modified = false;

            $exposed_headers = implode(', ', $this->getExposeHeaders());

            $this->response->setHeader(HttpHeaders::ACCESS_CONTROL_EXPOSE_HEADERS, $exposed_headers);
            if ($method == HttpMethods::GET) {
                $this->pagination_handler->setHeaders();
                $e_tag = strtoupper(md5(json_encode($content, JSON_PRESERVE_ZERO_FRACTION)));
                $this->response->setHeader(HttpHeaders::ETAG, $e_tag);
                $if_none_match = $this->getETag();

                if ($if_none_match == $e_tag) {
                    $is_not_modified = true;
                    $this->response->setNotModified();
                }
            }

            if (!$is_not_modified) {
                parent::onAfter();
            }

            if (!$this->response->isSent()) {
                $this->response->send();
            }
        }
    }

    private function checkDIRequirements()
    {
        $di = $this->getDI();
        foreach ($this->requiredServiceList() as $service_name => $service_type) {
            if (!$di->has($service_name)) {
                throw new Exception($service_name . ' service is required for di');
            }

            $service = $di->get($service_name);

            if (!($service instanceof $service_type)) {
                throw new Exception($service_name . ' must instanceof ' . $service_type);
            }
        }
    }

    private function requiredServiceList()
    {
        return [
            'autoload_handler' => AutoloadHandler::class,
            'application' => Application::class,
            'status_code_handler' => HttpStatusCodeHandler::class,
            'too_many_request_handler' => TooManyRequestHandler::class,
            'cache' => BackendInterface::class,
            'pagination_handler' => PaginationHandler::class
        ];
    }

    public function getETag()
    {
        return $this->request->getHeader(HttpHeaders::IF_NONE_MATCH);
    }

    /**
     * @return bool
     */
    public function getRequireJSON()
    {
        return $this->require_json;
    }

    /**
     * @return bool
     */
    public function getRequireBody()
    {
        return $this->require_body;
    }

    /**
     * @param $require_json bool
     * @return Application
     */
    public function setRequireJSON($require_json)
    {
        $this->require_json = $require_json;
        return $this;
    }

    /**
     * @param $require_body bool
     * @return Application
     */
    public function setRequireBody($require_body)
    {
        $this->require_body = $require_body;
        return $this;
    }
}
