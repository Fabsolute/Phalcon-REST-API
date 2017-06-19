<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 12:20
 */

namespace Fabs\Rest;


use Fabs\Rest\Services\AutoloadHandler;
use Fabs\Rest\Services\HttpStatusCodeHandler;
use Fabs\Rest\Services\PaginationHandler;
use Fabs\Rest\Services\TooManyRequestHandler;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\Router;
use Phalcon\Di\FactoryDefault;

/**
 * Class DI
 * @package Fabs\Rest
 *
 * @property AutoloadHandler autoload_handler
 * @property Application application
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

        $this->setShared('application', function () {
            if (PHP_SAPI == 'cli') {
                return new BaseApplication($this);
            } else {
                return new Application($this);
            }
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
    }

    public function __get($name)
    {
        if ($this->has($name)) {
            return $this->get($name);
        }
        return null;
    }
}