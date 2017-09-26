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
    /** @var int */
    private $total_count = 0;
    /** @var int */
    private $per_page_default = 0;
    /** @var string */
    private $total_count_header = HttpHeaders::X_TOTAL_COUNT;

    public function setHeaders()
    {
        if ($this->total_count > 0) {
            $this->response->setHeader($this->total_count_header, $this->total_count);
        }
    }

    /**
     * @param int $total_count
     */
    public function setTotalCount($total_count)
    {
        $this->total_count = $total_count;
    }

    public function getPage()
    {
        return $this->request->getQuery('page', 'int', 0);
    }

    public function getPerPage()
    {
        return $per_page = $this->request->getQuery('per_page', 'int', $this->per_page_default);
    }

    /**
     * @param int $per_page_default
     * @author necipallef <necipallef@gmail.com>
     */
    public function setPerPageDefault($per_page_default)
    {
        $this->per_page_default = $per_page_default;
    }

    /**
     * @param string $total_count_header
     * @author necipallef <necipallef@gmail.com>
     */
    public function setTotalCountHeader($total_count_header)
    {
        $this->total_count_header = $total_count_header;
    }
}