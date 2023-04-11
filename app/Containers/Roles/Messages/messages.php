<?php

namespace App\Containers\Roles\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'ROLES' => [
                'ALL' => 'Roles loaded successfully',
                'ALL_FAILED' => 'Roles not found'
            ]
        ];
    }
}
