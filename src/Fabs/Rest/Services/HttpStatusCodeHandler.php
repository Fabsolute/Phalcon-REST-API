<?php

namespace Fabs\Rest\Services;

use Fabs\Rest\Constants\HttpStatusCodes;
use Fabs\Rest\Constants\ResponseStatus;
use Fabs\Rest\Exceptions\BadRequestException;
use Fabs\Rest\Exceptions\ConflictException;
use Fabs\Rest\Exceptions\ForbiddenException;
use Fabs\Rest\Exceptions\InternalServerErrorException;
use Fabs\Rest\Exceptions\MethodNotAllowedException;
use Fabs\Rest\Exceptions\NotFoundException;
use Fabs\Rest\Exceptions\TooManyRequestException;
use Fabs\Rest\Exceptions\UnauthorizedException;
use Fabs\Rest\Exceptions\UnprocessableEntityException;
use Fabs\Rest\Exceptions\UnsupportedMediaTypeException;
use Fabs\Rest\Models\ErrorResponseModel;
use Fabs\Serialize\SerializableObject;

class HttpStatusCodeHandler extends ServiceBase
{
    /**
     * @deprecated use NotFoundException
     * @param mixed $error_details
     * @throws NotFoundException
     */
    public function notFound($error_details = null)
    {
        throw new NotFoundException($error_details);
    }

    /**
     * @deprecated use UnauthorizedException
     * @param mixed $error_details
     * @throws UnauthorizedException
     */
    public function unauthorized($error_details = null)
    {
        throw new UnauthorizedException($error_details);
    }

    /**
     * @deprecated use ForbiddenException
     * @param mixed $error_details
     * @throws ForbiddenException
     */
    public function forbidden($error_details = null)
    {
        throw new ForbiddenException($error_details);
    }

    /**
     * @deprecated use TooManyRequestException
     * @param mixed $error_details
     * @throws TooManyRequestException
     */
    public function tooManyRequest($error_details = null)
    {
        throw new TooManyRequestException($error_details);
    }

    /**
     * @deprecated use BadRequestException
     * @param mixed $error_details
     * @throws BadRequestException
     */
    public function badRequest($error_details = null)
    {
        throw new BadRequestException($error_details);
    }

    /**
     * @deprecated use UnprocessableEntityException
     * @param mixed $error_details
     * @throws UnprocessableEntityException
     */
    public function unprocessableEntity($error_details = null)
    {
        throw new UnprocessableEntityException($error_details);
    }

    /**
     * @deprecated use UnsupportedMediaTypeException
     * @param mixed $error_details
     * @throws UnsupportedMediaTypeException
     */
    public function unsupportedMediaType($error_details = null)
    {
        throw new UnsupportedMediaTypeException($error_details);
    }

    /**
     * @deprecated use MethodNotAllowedException
     * @param mixed $error_details
     * @throws MethodNotAllowedException
     */
    public function methodNotAllowed($error_details = null)
    {
        throw new MethodNotAllowedException($error_details);
    }

    /**
     * @deprecated use ConflictException
     * @param mixed $error_details
     * @throws ConflictException
     */
    public function conflict($error_details = null)
    {
        throw new ConflictException($error_details);
    }

    /**
     * @deprecated use InternalServerErrorException
     * @param mixed $error_details
     * @throws InternalServerErrorException
     */
    public function internalServerError($error_details = null)
    {
        throw new InternalServerErrorException($error_details);
    }

    /**
     * @deprecated use NotModifiedResponse
     */
    public function notModified()
    {
        $this->response->setNotModified()->send();
    }

    /**
     * @deprecated use OKResponse
     */
    public function success()
    {
        $this->response->setStatusCode(200)->send();
    }

    /**
     * @deprecated use CreatedResponse
     */
    public function created()
    {
        $this->response->setStatusCode(201)->send();
    }

    /**
     * @deprecated use AcceptedResponse
     */
    public function accepted()
    {
        $this->response->setStatusCode(202)->send();
    }
}
