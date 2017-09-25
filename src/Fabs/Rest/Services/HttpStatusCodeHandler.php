<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 10:09
 */

namespace Fabs\Rest\Services;

use Fabs\Rest\Constants\HttpStatusCodes;
use Fabs\Rest\Constants\ResponseStatus;
use Fabs\Rest\Models\ErrorResponseModel;
use Fabs\Serialize\SerializableObject;

class HttpStatusCodeHandler extends ServiceBase
{
    /**
     * @param $error_code int
     * @param $error_message string
     * @param $error_details array|SerializableObject|null
     */
    public function handleError($error_code, $error_message, $error_details = null)
    {
        $error_response_model = new ErrorResponseModel();
        $error_response_model->status = ResponseStatus::FAILURE;

        if ($error_details !== null) {
            if ($error_details instanceof SerializableObject) {
                $error_response_model->error_message = $error_message;
                $error_response_model->error_details = $error_details;
            } elseif (is_array($error_details) && count($error_details) > 0) {
                $error_response_model->error_list = $error_details;
                $error_response_model->error = $error_message;
            }
        }

        if (!$this->response->isSent()) {
            $this->response
                ->setStatusCode($error_code)
                ->setJsonContent($error_response_model)->send();
        }
    }

    public function notFound($error_details = null)
    {
        $this->handleError(404, HttpStatusCodes::NotFound, $error_details);
    }

    public function unauthorized($error_details = null)
    {
        $this->handleError(401, HttpStatusCodes::Unauthorized, $error_details);
    }

    public function forbidden($error_details = null)
    {
        $this->handleError(403, HttpStatusCodes::Forbidden, $error_details);
    }

    public function tooManyRequest($error_details = null)
    {
        $this->handleError(429, HttpStatusCodes::TooManyRequest, $error_details);
    }

    public function badRequest($error_details = null)
    {
        $this->handleError(400, HttpStatusCodes::BadRequest, $error_details);
    }

    public function unprocessableEntity($error_details = null)
    {
        $this->handleError(422, HttpStatusCodes::UnprocessableEntity, $error_details);
    }

    public function unsupportedMediaType($error_details = null)
    {
        $this->handleError(415, HttpStatusCodes::UnsupportedMediaType, $error_details);
    }

    public function methodNotAllowed($error_details = null)
    {
        $this->handleError(405, HttpStatusCodes::MethodNotAllowed, $error_details);
    }

    /**
     * @param array|null $error_details
     */
    public function conflict($error_details = null)
    {
        $this->handleError(409, HttpStatusCodes::Conflict, $error_details);
    }

    public function internalServerError($error_details = null)
    {
        $this->handleError(500, HttpStatusCodes::InternalServerError, $error_details);
    }

    public function notModified()
    {
        $this->response->setNotModified()->send();
    }

    public function success()
    {
        $this->response->setStatusCode(200)->send();
    }

    public function created()
    {
        $this->response->setStatusCode(201)->send();
    }

    public function accepted()
    {
        $this->response->setStatusCode(202)->send();
    }
}
