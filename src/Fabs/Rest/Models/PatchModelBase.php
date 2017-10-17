<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 06/06/2017
 * Time: 14:59
 */

namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class PatchModelBase extends SerializableObject
{
    public function applyAddOperation($subject)
    {
        return true;
    }

    public function applyRemoveOperation($subject)
    {
        return true;
    }

    public function applyReplaceOperation($subject)
    {
        return true;
    }

    public function applyCustomOperation($operation_name, $subject)
    {
        return true;
    }
}