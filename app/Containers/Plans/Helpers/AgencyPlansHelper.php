<?php

namespace App\Containers\Plans\Helpers;

use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\CreateFailedException;

use Carbon\Carbon;

use App\Containers\Plans\Models\AgencyPlan;

use App\Helpers\BaseHelper;

class AgencyPlansHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'AGENCY_PLANS';
    protected static string $modelName = 'AgencyPlan';
    protected static string $model = AgencyPlan::class;
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
     * Checks for starting and ending dates
     * throws Exception if not
     * 
     * @param AgencyPlan $plan
     * @param array $data
     * @return boolean | Exception
     */
    public static function checkUploadedData(AgencyPlan $plan = null, array $data)
    {
        $checksFailed = false;
        if(
            !isset($data['plan_id']) ||
            !isset($data['agency_id']) ||
            !isset($data['starting_date']) ||
            !isset($data['ending_date'])
            ) {
                $checksFailed = true;
        }
        if($plan == null && $checksFailed) {
            throw new CreateFailedException('', self::$messageKeyBase . '.REQUIRED');
        }
        if($plan != null && $checksFailed) {
            throw new UpdateFailedException('', self::$messageKeyBase . '.REQUIRED');
        }

        $startingDate = new Carbon($data['starting_date']);
        $endingDate = new Carbon($data['ending_date']);

        if($plan == null && $startingDate->isPast()) {
            throw new CreateFailedException('', self::$messageKeyBase . '.PAST');
        }
        if($plan != null && $startingDate->isPast()) {
            throw new UpdateFailedException('', self::$messageKeyBase . '.PAST');
        }
        
        $result = $endingDate->lte($startingDate);
        if($plan == null && $result) {
            throw new CreateFailedException('', self::$messageKeyBase . '.STARTING_DATE_BEFORE_ENDING');
        }
        if($plan != null && $result) {
            throw new UpdateFailedException('', self::$messageKeyBase . '.STARTING_DATE_BEFORE_ENDING');
        }

        return true;
    }
    /**
     * Trims the data of the plan
     * and sets its dates
     * 
     * @param array $data
     * @return array $data
     */
    public static function trimAndSetup(array $data)
    {
        isset($data['note']) ? $data['note'] = trim($data['note']) : '';
        isset($data['starting_date']) ? $data['starting_date'] = new Carbon($data['starting_date']) : null;
        isset($data['ending_date']) ? $data['ending_date'] = new Carbon($data['ending_date']) : null;
        return $data;
    }
}