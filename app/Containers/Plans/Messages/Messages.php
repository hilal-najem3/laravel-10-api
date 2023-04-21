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
            ]
        ];
    }
}