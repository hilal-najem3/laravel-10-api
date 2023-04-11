<?php

namespace App\Containers\Permissions\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'PERMISSIONS' => [
                'ALL' => 'Permissions loaded successfully',
                'ALL_FAILED' => 'Permissions not found'
            ]
        ];
    }
}
