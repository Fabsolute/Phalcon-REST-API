<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 19/06/2017
 * Time: 15:38
 */

namespace Fabs\Rest;


use Fabs\Rest\Services\ServiceBase;

abstract class TaskBase extends ServiceBase
{
    public abstract function getName();
    public abstract function execute();
}