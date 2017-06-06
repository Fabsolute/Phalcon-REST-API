<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 10:09
 */

namespace Fabs\Rest\Services;

use Fabs\Rest\Constants\HttpStatusCodes;

class HttpStatusCodeHandler extends ServiceBase
{
    /**
     * @param $error_code int
     * @param $error_message string
     * @param $error_list array
     */
    public function handleError($error_code, $error_message, $error_list = null)
    {
        $error_content = [
            'status' => 'failure',
            'error' => $error_message
        ];

        if (is_array($error_list) && count($error_list) > 0) {
            $error_content['error_list'] = $error_list;
        }

        if (!$this->response->isSent()) {
            $this->response
                ->setStatusCode($error_code)
                ->setJsonContent($error_content)->send();
        }
    }

    public function notFound($error_list = null)
    {
        $this->handleError(404, HttpStatusCodes::NotFound, $error_list);
    }

    public function unauthorized($error_list = null)
    {
        $this->handleError(401, HttpStatusCodes::Unauthorized, $error_list);
    }

    public function forbidden($error_list = null)
    {
        $this->handleError(403, HttpStatusCodes::Forbidden, $error_list);
    }

    public function tooManyRequest($error_list = null)
    {
        $this->handleError(429, HttpStatusCodes::TooManyRequest, $error_list);
    }

    public function badRequest($error_list = null)
    {
        $this->handleError(400, HttpStatusCodes::BadRequest, $error_list);
    }

    public function unprocessableEntity($error_list = null)
    {
        $this->handleError(422, HttpStatusCodes::UnprocessableEntity, $error_list);
    }

    public function unsupportedMediaType($error_list = null)
    {
        $this->handleError(415, HttpStatusCodes::UnsupportedMediaType, $error_list);
    }

    public function methodNotAllowed($error_list = null)
    {
        $this->handleError(405, HttpStatusCodes::MethodNotAllowed, $error_list);
    }

    /**
     * @param array|null $error_list
     */
    public function conflict($error_list = null)
    {
        $this->handleError(409, HttpStatusCodes::Conflict, $error_list);
    }

    public function notModified()
    {
        $this->response->setNotModified()->send();
    }

    public function success()
    {
        $this->response->setStatusCode(200)->send();
    }
}