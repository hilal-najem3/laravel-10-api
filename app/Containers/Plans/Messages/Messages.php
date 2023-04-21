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
                'NOT_FOUND' => 'Plan not found'
            ]
        ];
    }
}