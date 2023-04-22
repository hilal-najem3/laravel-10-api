<?php

namespace App\Containers\Plans\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'PLANS' => [
                'NAME' => 'Plan',
                'GET' => 'Plans loaded successfully',
                'NOT_FOUND' => 'Plan not found',
                'CREATE_FAILED' => 'Plan creation failed',
                'CREATE_SUCCESSFUL' => 'Plan created successfully',
                'UPDATE_FAILED' => 'Plan update failed',
                'UPDATE_SUCCESSFUL' => 'Plan updated successfully',
                'UNIQUE_NAME' => 'Plan name already exists',
                'UNIQUE_ABBREVIATION' => 'Plan abbreviation already exists',
            ],
            'AGENCY_PLANS' => [
                'NAME' => 'Agency plan',
                'GET' => 'Agency plans loaded successfully',
                'NOT_FOUND' => 'Agency plan not found',
                'CREATE_FAILED' => 'Agency plan creation failed',
                'CREATE_SUCCESSFUL' => 'Agency plan created successfully',
                'UPDATE_FAILED' => 'Agency plan update failed',
                'UPDATE_SUCCESSFUL' => 'Agency plan updated successfully',
                'REQUIRED' => 'Agency plan requires agency_id, plan_id, starting_date and ending_date to create or update',
                'PAST' => 'Agency plan starting date can\'t be in the past',
                'STARTING_DATE_BEFORE_ENDING' => 'Agency plan starting date should be before ending date'
            ],
        ];
    }
}