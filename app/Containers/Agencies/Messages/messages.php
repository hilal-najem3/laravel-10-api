<?php

namespace App\Containers\Agencies\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'AGENCY_TYPES' => [
                'NOT_FOUND' => 'Agency type',
                'GET' => 'Agency Type/s loaded successfully',
                'CREATE_SUCCESSFUL' => 'Agency type created successfully',
                'CREATE_FAILED' => 'Agency type',
                'DUPLICATE_NAME' => 'There is another agency type with the same name',
                'UPDATE_SUCCESSFUL' => 'Agency type updated successfully',
                'UPDATE_FAILED' => 'Agency type',
            ],
            'AGENCY' => [
                'NOT_FOUND' => 'Agency',
                'GET' => 'Agency loaded successfully',
                'CREATE_SUCCESSFUL' => 'Agency created successfully',
                'CREATE_FAILED' => 'Agency',
                'UPDATE_SUCCESSFUL' => 'Agency updated successfully',
                'UPDATE_FAILED' => 'Agency',
                'DUPLICATE_USERNAME' => 'There is another agency with the same username',
                'LOGO' => 'Logo image uploaded successfully',
                'LOGO_ERROR' => 'Agency logo image',
            ],
            'AGENCY_CURRENCY' => [
                'DEFAULT_NAME' => 'Default currency for the agency',
                'DEFAULT' => 'Default currency for the agency updated successfully',
                'DEFAULT_FAILED' => 'Default currency for the agency update failed',
                'DEFAULT_GET' => 'Default currency for the agency loaded successfully',
                'DEFAULT_GET_FAILED' => 'Default currency for the agency not found',
                'DEFAULT_NOT_ALLOWED' => 'You are not allowed to change the default currency for an agency you are not an admin for it',
                'CURRENCY_CONVERSION' => [
                    'GET' => 'Currency conversions loaded successfully',
                    'GET_NOT_FOUND' => 'Currency conversions',
                    'UPDATE_ACTIVE_SUCCESSFUL' => 'Active currency conversion updated successfully',
                    'UPDATE_ACTIVE_FAIL' => 'Active currency conversion update failed',
                    'UPDATE_NOT_ALLOWED' => 'You are not allowed to update the currency conversions for this agency',
                    'WRONG_CURRENCIES' => 'Currencies not found',
                    'SAME_CURRENCIES' => 'Currencies from and to are exactly the same',
                    'INVALID_OPERATION' => 'Conversion operation can be \'*\' or \'/\' only',
                    'HISTORY_NOT_ALLOWED' => 'You are not allowed to access to edit info concerning conversion\'s history',
                    'HISTORY_GET' => 'Conversions history loaded successfully',
                    'HISTORY_FAIL' => 'Conversions history load failed',
                ]
            ],
            'AGENCY_ADMIN' => [
                'NOT_ALLOWED' => 'You are not allowed to change this information'
            ]
        ];
    }
}
