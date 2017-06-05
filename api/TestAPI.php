<?php
use Fabs\Rest\Constants\HttpMethods;

/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 13:02
 */
class TestAPI extends \Fabs\Rest\APIBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    protected function getPrefix()
    {
        return '/fuck';
    }

    public function get()
    {
        return [
            'helal len'
        ];
    }
}