<?php


namespace Fabs\Rest\Models;


use Fabs\Rest\Conditions\RenderIfNotValue;

class TaskResponseModel extends ResponseModel
{
    /** @var int */
    public $max_retry_count = 0;
    /** @var int */
    public $retry_after_seconds = 0;

    public function __construct()
    {
        parent::__construct();

        $this->addCondition('max_retry_count', new RenderIfNotValue(0));
        $this->addCondition('retry_after_seconds', new RenderIfNotValue(0));

        $this->addIntegerValidation('max_retry_count');
        $this->addIntegerValidation('retry_after_seconds');
    }
}
