<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 11:55
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\Constants\HttpHeaders;

class PaginationHandler extends ServiceBase
{
    private $total_count = 0;

    public function setHeaders()
    {
        if ($this->total_count > 0) {
            $this->response->setHeader(HttpHeaders::X_TOTAL_COUNT, $this->total_count);
        }
    }

    /**
     * @param int $total_count
     */
    public function setTotalCount($total_count)
    {
        $this->total_count = $total_count;
    }
}