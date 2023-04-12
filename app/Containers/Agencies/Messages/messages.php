<?php

namespace App\Containers\Agencies\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'AGENCY_TYPES' => [
                'NOT_FOUND' => 'Agent type not found',
                'CREATE_FAILED' => 'Agency type create failed',
                'DUPLICATE_NAME' => 'There is another agency type with the same name',
                'UPDATE_FAILED' => 'Agency type update failed',
            ]
        ];
    }
}