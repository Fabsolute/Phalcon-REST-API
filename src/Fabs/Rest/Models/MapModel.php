<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 05/06/2017
 * Time: 13:35
 */

namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class MapModel extends SerializableObject
{
    public $url = null;
    public $function_name = null;
    public $method_name = null;
}