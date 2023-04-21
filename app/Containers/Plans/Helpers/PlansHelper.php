<?php

namespace App\Containers\Plans\Helpers;

use App\Containers\Plans\Models\Plan;

use App\Helpers\BaseHelper;

class PlansHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'PLANS';
    protected static string $modelName = 'Plan';
    protected static string $model = Plan::class;
    protected static $allowed = ['id', 'all', 'create'];

    protected static function model()
    {
        return self::$model;
    }

    protected static function message()
    {
        return self::$messageKeyBase;
    }

    protected static function allowed()
    {
        return self::$allowed;
    }
}