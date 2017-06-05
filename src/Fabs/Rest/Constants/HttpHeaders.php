<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 10:21
 */

namespace Fabs\Rest\Constants;


class HttpHeaders
{
    const ACCESS_CONTROL_ALLOW_ORIGIN   = 'Access-Control-Allow-Origin';
    const ACCESS_CONTROL_ALLOW_HEADERS  = 'Access-Control-Allow-Headers';
    const ACCESS_CONTROL_ALLOW_METHODS  = 'Access-Control-Allow-Methods';
    const ACCESS_CONTROL_EXPOSE_HEADERS = 'Access-Control-Expose-Headers';
    const IF_NONE_MATCH                 = 'If-None-Match';
    const CONTENT_TYPE                  = 'Content-Type';
    const SESSION_ID                    = 'Session-ID';
    const ACCESS_TOKEN                  = 'Access-Token';
    const ETAG                          = 'ETag';
    const X_RATELIMIT_LIMIT             = 'X-RateLimit-Limit';
    const X_RATELIMIT_REMAINING         = 'X-RateLimit-Remaining';
    const X_RATELIMIT_RESET             = 'X-RateLimit-Reset';
    const X_TOTAL_COUNT                 = 'X-Total-Count';
}