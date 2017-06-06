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
use Fabs\Serialize\SerializableObject;
use Fabs\Serialize\Validation\ValidationException;

class PatchMethodHandler extends ServiceBase
{
    /** @var string[] */
    private $allowed_operations = [];
    /** @var SerializableObject[]|array */
    private $add_operations = [];
    /** @var SerializableObject[]|array */
    private $remove_operations = [];
    /** @var SerializableObject[]|array */
    private $replace_operations = [];
    /** @var SerializableObject[][]|array */
    private $custom_operations = [];

    /** @var string */
    private $update_model_type = null;

    /**
     * @param string $operation
     * @return PatchMethodHandler
     */
    public function addAllowedOperation($operation)
    {
        $this->allowed_operations[$operation] = $operation;
        return $this;
    }

    /**
     * @param string $operation
     * @return PatchMethodHandler
     */
    public function removeAllowedOperation($operation)
    {
        unset($this->allowed_operations[$operation]);
        return $this;
    }

    public function setUpdateModelType($update_model_type)
    {
        if (is_subclass_of($update_model_type, SerializableObject::class)) {
            $this->update_model_type = $update_model_type;
        }
        throw new \InvalidArgumentException('update_model_type must instance of SerializableObject');
    }

    public function handle()
    {
        $request_data = $this->application->getRequestData();
        /** @var PatchDataModel[] $patch_data_list */
        $patch_data_list = PatchDataModel::deserialize($request_data, true);

        foreach ($patch_data_list as $patch_data) {
            if ($patch_data == null) {
                $this->status_code_handler->unprocessableEntity(['error' => 'Invalid body for PATCH']);
                return false;
            }

            if (!in_array($patch_data->op, $this->allowed_operations, true)) {
                $this->status_code_handler->unprocessableEntity([
                    'error' => 'op not allowed',
                    'value' => $patch_data->op
                ]);
                return false;
            }

            if (!$this->isValidPath($patch_data->path)) {
                $this->status_code_handler->unprocessableEntity([
                    'error' => 'Invalid path for PATCH',
                    'value' => $patch_data->path
                ]);
                return false;
            }

            $patch_data_value = $this->patchDataToArray($patch_data->value);
            if ($this->update_model_type != null) {
                try {
                    $patch_data_value = SerializableObject::create($patch_data_value, $this->update_model_type);
                } catch (ValidationException $exception) {
                    $this->status_code_handler->unprocessableEntity([
                        'error' => 'Incompatible path and/or value',
                        'path' => $patch_data->path,
                        'value' => $patch_data->value,
                        'expected' => $exception->getValidatorName(),
                        'property' => $exception->getPropertyName()
                    ]);
                }
            }

            switch ($patch_data->op) {
                case PatchOperations::ADD:
                    $this->add_operations[] = $patch_data_value;
                    break;
                case PatchOperations::REMOVE:
                    $this->remove_operations[] = $patch_data_value;
                    break;
                case PatchOperations::REPLACE:
                    $this->replace_operations[] = $patch_data_value;
                    break;
                default:
                    $this->custom_operations[$patch_data->op][] = $patch_data_value;
                    break;
            }
        }
        return true;
    }

    /**
     * @return array|\Fabs\Serialize\SerializableObject[]
     */
    public function getAddOperations()
    {
        return $this->add_operations;
    }

    /**
     * @return array|\Fabs\Serialize\SerializableObject[]
     */
    public function getRemoveOperations()
    {
        return $this->remove_operations;
    }

    /**
     * @return array|\Fabs\Serialize\SerializableObject[]
     */
    public function getReplaceOperations()
    {
        return $this->replace_operations;
    }

    /**
     * @return array|\Fabs\Serialize\SerializableObject[][]
     */
    public function getCustomOperations()
    {
        return $this->custom_operations;
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
}