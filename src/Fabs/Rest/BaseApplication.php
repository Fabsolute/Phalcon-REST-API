<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/06/2017
 * Time: 15:37
 */

namespace Fabs\Rest;


use ErrorException;
use Fabs\Rest\Services\AutoloadHandler;
use Fabs\Rest\Services\HttpStatusCodeHandler;
use Fabs\Rest\Services\PaginationHandler;
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
 */
class BaseApplication extends Micro
{
    public function __construct($di)
    {
        $this->after([$this, 'onAfter']);
        $this->before([$this, 'onBefore']);
        $this->notFound([$this, 'onNotFound']);

        set_error_handler([$this, 'onPHPError']);
        parent::__construct($di);
    }

    public function onAfter()
    {
        $content = $this->getReturnedValue();
        $this->response->setJsonContent(
            [
                'status' => 'success',
                'data' => $content
            ],
            JSON_PRESERVE_ZERO_FRACTION
        )->send();
    }

    public function onBefore()
    {
        return true;
    }

    public function onNotFound()
    {
        $this->status_code_handler->notFound();
    }

    public function onPHPError($error_no, $error_message, $error_file, $error_line)
    {
        throw new ErrorException($error_message, 0, $error_no, $error_file, $error_line);
    }

    public function handle($uri = null)
    {
        if ($uri === null) {
            if (PHP_SAPI === 'cli') {
                global $argv;
                $uri = $this->createUriFromArgv($argv);
            }
        }
        $this->autoload_handler->mount();
        parent::handle($uri);
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