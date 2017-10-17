<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 06/06/2017
 * Time: 10:47
 */

namespace Fabs\Rest\Models;

use Fabs\Serialize\SerializableObject;

class PatchDataModel extends SerializableObject
{
    /** @var  string */
    public $op;
    /** @var  string */
    public $path;
    /** @var  mixed */
    public $value;

    function __construct()
    {
        parent::__construct();

        $this->addStringValidation('op')->isRequired();
        $this->addStringValidation('path')->isRequired();
    }
}