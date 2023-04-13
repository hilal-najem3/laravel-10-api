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
            ]
        ];
    }
}
