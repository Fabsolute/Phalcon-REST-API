<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/06/2017
 * Time: 15:37
 */

namespace Fabs\Rest;


use ErrorException;
use Fabs\Rest\Constants\ResponseStatus;
use Fabs\Rest\Exceptions\NotFoundException;
use Fabs\Rest\Exceptions\StatusCodeException;
use Fabs\Rest\Models\AcceptedResponse;
use Fabs\Rest\Models\CreatedResponse;
use Fabs\Rest\Models\ErrorResponseModel;
use Fabs\Rest\Models\NoContentResponse;
use Fabs\Rest\Models\NotModifiedResponse;
use Fabs\Rest\Models\OKResponse;
use Fabs\Rest\Models\ResponseModel;
use Fabs\Rest\Services\AutoloadHandler;
use Fabs\Rest\Services\ExceptionHandler;
use Fabs\Rest\Services\HttpStatusCodeHandler;
use Fabs\Rest\Services\PaginationHandler;
use Fabs\Rest\Services\RuleHandler;
use Fabs\Rest\Services\TooManyRequestHandler;
use Phalcon\Cache\BackendInterface;
use Phalcon\Mvc\Micro;

/**
 * Class ApplicationBase
 * @package Fabs\Rest
 *
 * @property AutoloadHandler autoload_handler
 * @property Application application
 * @property HttpStatusCodeHandler status_code_handler
 * @property TooManyRequestHandler too_many_request_handler
 * @property BackendInterface cache
 * @property PaginationHandler pagination_handler
 * @property RuleHandler rule_handler
 * @property ExceptionHandler exception_handler
 */
class BaseApplication extends Micro
{
    public function __construct($di)
    {
        $this->after([$this, 'onAfter']);
        $this->before([$this, 'onBefore']);
        $this->notFound([$this, 'onNotFound']);

        set_error_handler([$this, 'onPHPError']);
        set_exception_handler([$this, 'onException']);
        parent::__construct($di);
    }

    public function onAfter()
    {
        $returned_value = $this->getReturnedValue();
        if ($returned_value instanceof NoContentResponse) {
            $this->response->setStatusCode(204)->send();
        } elseif ($returned_value instanceof OKResponse) {
            $this->response->setStatusCode(200)->send();
        } elseif ($returned_value instanceof CreatedResponse) {
            $this->response->setStatusCode(201)->send();
        } elseif ($returned_value instanceof AcceptedResponse) {
            $this->response->setStatusCode(202)->send();
        } elseif ($returned_value instanceof NotModifiedResponse) {
            $this->response->setNotModified()->send();
        } else {
            $response_model = new ResponseModel();
            $response_model->status = ResponseStatus::SUCCESS;
            $response_model->data = $this->getReturnedValue();

            $this->response->setJsonContent(
                $response_model,
                JSON_PRESERVE_ZERO_FRACTION
            )->send();
        }
    }

    public function onBefore()
    {
        return true;
    }

    public function onNotFound()
    {
        throw new NotFoundException();
    }

    public function onPHPError($error_no, $error_message, $error_file, $error_line)
    {
        throw new ErrorException($error_message, 0, $error_no, $error_file, $error_line);
    }

    /**
     * @param StatusCodeException $exception
     * @author ahmetturk <ahmetturk93@gmail.com>
     * @return bool
     */
    public function onStatusCodeException($exception)
    {
        $error_response_model = new ErrorResponseModel();
        $error_response_model->status = ResponseStatus::FAILURE;
        $error_response_model->error_message = $exception->getMessage();
        $error_response_model->error_details = $exception->getErrorDetails();

        if (!$this->response->isSent()) {
            $this->response
                ->setStatusCode($exception->getCode())
                ->setJsonContent($error_response_model)
                ->send();
        }

        return true;
    }

    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    public function onException($exception)
    {
        $this->exception_handler->handle($exception);
    }

    public function handle($uri = null)
    {
        try {
            if ($uri === null) {
                if (PHP_SAPI === 'cli') {
                    global $argv;
                    $uri = $this->createUriFromArgv($argv);
                }
            }
            $this->autoload_handler->mount();
            parent::handle($uri);
        } catch (StatusCodeException $exception) {
            $is_handled = $this->onStatusCodeException($exception);
            if ($is_handled !== true) {
                $this->onException($exception);
            }
        } catch (\Exception $exception) {
            $this->onException($exception);
        }
    }

    private function createUriFromArgv($argv)
    {
        if ($argv != null) {

            $args = array_slice($argv, 1);
            if (count($args) > 0) {
                if (strpos($args[0], 'task=') !== 0) {
                    $args[0] = 'task=' . $args[0];
                }

                $arguments = join("&", $args);
                parse_str($arguments, $_GET);
            }
        }

        if (count($_GET) == 0) {
            echo 'count($_GET) is 0. Aborting';
            exit;
        }
        $task_name = $this->request->getQuery('task');
        $uri = '/task/' . $task_name;
        return $uri;
    }
}