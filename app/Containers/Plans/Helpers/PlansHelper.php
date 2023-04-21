<?php

namespace App\Containers\Plans\Helpers;

use App\Exceptions\Common\UpdateFailedException;

use App\Containers\Plans\Models\Plan;

use App\Helpers\BaseHelper;

class PlansHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'PLANS';
    protected static string $modelName = 'Plan';
    protected static string $model = Plan::class;
    protected static $allowed = ['id', 'all', 'create', 'update'];

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

    /**
     * Checks if the name and the abbreviation are unique
     * throws UpdateFailedException if not
     * 
     * @param Plan $plan
     * @param array $data
     * @return boolean | UpdateFailedException
     */
    public static function checkUpdateData(Plan $plan, array $data)
    {
        if(isset($data['name']) && $data['name'] != $plan->name) {
            $count = Plan::where('name', $data['name'])->count();
            if($count > 0) {
                throw new UpdateFailedException('', 'PLANS.UNIQUE_NAME');
            }
        }

        if(isset($data['abbreviation']) && $data['abbreviation'] != $plan->abbreviation) {
            $count = Plan::where('abbreviation', $data['abbreviation'])->count();
            if($count > 0) {
                throw new UpdateFailedException('', 'PLANS.UNIQUE_ABBREVIATION');
            }
        }
        
        return true;
    }

    /**
     * Trims the data of the plan from
     * 
     * @param array $data
     * @return array $data
     */
    public static function trim(array $data)
    {
        isset($data['name']) ? $data['name'] = trim($data['name']) : '';
        isset($data['abbreviation']) ? $data['abbreviation'] = trim($data['abbreviation']) : '';
        isset($data['description']) ? $data['description'] = trim($data['description']) : '';
        return $data;
    }
}