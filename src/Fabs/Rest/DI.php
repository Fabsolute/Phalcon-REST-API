<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 12:20
 */

namespace Fabs\Rest;


use Fabs\Rest\Services\AutoloadHandler;
use Fabs\Rest\Services\ExceptionHandler;
use Fabs\Rest\Services\HttpStatusCodeHandler;
use Fabs\Rest\Services\PaginationHandler;
use Fabs\Rest\Services\RuleHandler;
use Fabs\Rest\Services\TooManyRequestHandler;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\Router;
use Phalcon\Di\FactoryDefault;

/**
 * Class DI
 * @package Fabs\Rest
 *
 * @property AutoloadHandler autoload_handler
 * @property BaseApplication system
 * @property Application application
 * @property BaseApplication task_application
 * @property HttpStatusCodeHandler status_code_handler
 * @property TooManyRequestHandler too_many_request_handler
 * @property BackendInterface cache
 * @property PaginationHandler pagination_handler
 */
class DI extends FactoryDefault
{
    public function __construct()
    {
        parent::__construct();

        $this->setShared('autoload_handler', function () {
            return new AutoloadHandler();
        });

        $this->setShared('system', function () {
            if (PHP_SAPI == 'cli') {
                return $this->task_application;
            } else {
                return $this->application;
            }
        });

        $this->setShared('application', function () {
            return new Application($this);
        });

        $this->setShared('task_application', function () {
            return new BaseApplication($this);
        });

        $this->setShared('status_code_handler', function () {
            return new HttpStatusCodeHandler();
        });

        $this->setShared('pagination_handler', function () {
            return new PaginationHandler();
        });

        $this->setShared('router', function () {
            return new Router(false);
        });

        $this->set('too_many_request_handler', function () {
            return new TooManyRequestHandler();
        });

        $this->set('rule_handler', function () {
            return new RuleHandler();
        });

        $this->set('exception_handler', function () {
            return new ExceptionHandler();
        });
    }

    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }
        return null;
    }
}