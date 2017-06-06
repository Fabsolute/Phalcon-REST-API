<?php
/**
 * Created by PhpStorm.
 * User: ahmetturk
 * Date: 06/06/2017
 * Time: 10:24
 */

namespace Fabs\Rest\Services;


use Fabs\Rest\Constants\PatchOperations;
use Fabs\Rest\Models\PatchDataModel;
use Fabs\Rest\Models\PatchHandlerResponseModel;
use Fabs\Serialize\SerializableObject;

class PatchHandler extends ServiceBase
{
    /** @var string[] */
    private $allowed_operations = [];

    /** @var string */
    private $update_model_type = null;

    /**
     * @param string $operation
     * @return PatchHandler
     */
    public function addAllowedOperation($operation)
    {
        $this->allowed_operations[$operation] = $operation;
        return $this;
    }

    /**
     * @param string $operation
     * @return PatchHandler
     */
    public function removeAllowedOperation($operation)
    {
        unset($this->allowed_operations[$operation]);
        return $this;
    }

    /**
     * @param string $update_model_type
     * @return PatchHandler
     */
    public function setUpdateModelType($update_model_type)
    {
        if (is_subclass_of($update_model_type, SerializableObject::class)) {
            $this->update_model_type = $update_model_type;
            return $this;
        }
        throw new \InvalidArgumentException('update_model_type must instance of SerializableObject');
    }

    /**
     * @return PatchHandlerResponseModel
     */
    public function handle()
    {
        $request_data = $this->application->getRequestData();
        /** @var PatchDataModel[] $patch_data_list */
        $patch_data_list = PatchDataModel::deserialize($request_data, true);

        $add_operations = [];
        $remove_operations = [];
        $replace_operations = [];
        $custom_operations = [];

        foreach ($patch_data_list as $patch_data) {
            if ($patch_data == null) {
                $this->status_code_handler->unprocessableEntity(['error' => 'Invalid body for PATCH']);
                return null;
            }

            if (!in_array($patch_data->op, $this->allowed_operations, true)) {
                $this->status_code_handler->unprocessableEntity([
                    'error' => 'op not allowed',
                    'value' => $patch_data->op
                ]);
                return null;
            }

            if (!$this->isValidPath($patch_data->path)) {
                $this->status_code_handler->unprocessableEntity([
                    'error' => 'Invalid path for PATCH',
                    'value' => $patch_data->path
                ]);
                return null;
            }

            $patch_data_value = $this->patchDataToArray($patch_data);

            switch ($patch_data->op) {
                case PatchOperations::ADD:
                    $add_operations[] = $patch_data_value;
                    break;
                case PatchOperations::REMOVE:
                    $remove_operations[] = $patch_data_value;
                    break;
                case PatchOperations::REPLACE:
                    $replace_operations[] = $patch_data_value;
                    break;
                default:
                    $custom_operations[$patch_data->op][] = $patch_data_value;
                    break;
            }
        }

        $add_operation_data = $this->merge($add_operations);
        $remove_operation_data = $this->merge($remove_operations);
        $replace_operation_data = $this->merge($replace_operations);
        $custom_operations_data = $this->merge($custom_operations);

        if ($this->update_model_type != null) {
            $add_operation_data = SerializableObject::create($add_operation_data, $this->update_model_type);
            $remove_operation_data = SerializableObject::create($remove_operation_data, $this->update_model_type);
            $replace_operation_data = SerializableObject::create($replace_operation_data, $this->update_model_type);
            foreach ($custom_operations_data as $key => $value) {
                $custom_operations_data[$key] = SerializableObject::create($value, $this->update_model_type);
            }
        }

        $patch_handler_response = new PatchHandlerResponseModel();
        $patch_handler_response->add_operation_model = $add_operation_data;
        $patch_handler_response->remove_operation_model = $remove_operation_data;
        $patch_handler_response->replace_operation_model = $replace_operation_data;
        $patch_handler_response->custom_operation_models = $custom_operations_data;

        return $patch_handler_response;
    }

    private function isValidPath($path)
    {
        return strpos($path, '/') === 0 &&
        substr($path, strlen($path) - 1) != '/' &&
        strpos($path, '//') === false;
    }

    /**
     * @param PatchDataModel $patch_data
     * @return array
     */
    private function patchDataToArray($patch_data)
    {
        $array = [];
        $paths = explode('/', $patch_data->path);
        $index = count($paths) - 1;
        $array[$paths[$index]] = $patch_data->value;
        while ($index > 1) {

            $array[$paths[$index - 1]] = $array;
            unset($array[$paths[$index]]);
            $index--;
        }

        return $array;
    }

    private function merge($array_of_array)
    {
        return call_user_func_array('array_merge_recursive', $array_of_array);
    }
}