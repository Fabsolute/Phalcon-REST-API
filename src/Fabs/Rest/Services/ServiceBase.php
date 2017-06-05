<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 11:29
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\Application;
use Phalcon\Cache\BackendInterface;
use Phalcon\Di\Injectable;

/**
 * Class ServiceBase
 * @package Fabs\Rest\Services
 *
 * @property APIHandler api_handler
 * @property Application application
 * @property HttpStatusCodeHandler status_code_handler
 * @property TooManyRequestHandler too_many_request_handler
 * @property BackendInterface cache
 * @property PaginationHandler pagination_handler
 */
abstract class ServiceBase extends Injectable
{
}