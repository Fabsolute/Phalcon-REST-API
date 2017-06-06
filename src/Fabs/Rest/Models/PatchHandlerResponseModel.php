<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 06/06/2017
 * Time: 15:05
 */

namespace Fabs\Rest\Models;


use Fabs\Serialize\SerializableObject;

class PatchHandlerResponseModel extends SerializableObject
{
    /** @var PatchDataModel|array */
    public $add_operation_model;
    /** @var PatchDataModel|array */
    public $remove_operation_model;
    /** @var PatchDataModel|array */
    public $replace_operation_model;
    /** @var PatchDataModel[]|array */
    public $custom_operation_models = [];

    public function apply($subject)
    {
        $output = false;

        if ($this->add_operation_model instanceof PatchModelBase) {
            $output = $this->add_operation_model->applyAddOperation($subject);
        }

        if ($output === true) {
            if ($this->remove_operation_model instanceof PatchModelBase) {
                $output = $this->remove_operation_model->applyRemoveOperation($subject);
            }
        }
        if ($output === true) {
            if ($this->replace_operation_model instanceof PatchModelBase) {
                $output = $this->replace_operation_model->applyReplaceOperation($subject);
            }
        }
        foreach ($this->custom_operation_models as $operation => $custom_operation_model) {
            if ($output === true) {
                if ($custom_operation_model instanceof PatchModelBase) {
                    $output = $custom_operation_model->applyCustomOperation($operation, $subject);
                }
            }
        }
        return $output;
    }
}