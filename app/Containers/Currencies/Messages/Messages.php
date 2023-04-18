<?php

namespace App\Containers\Currencies\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'CURRENCY' => [
                'GET' => 'Currency data loaded succcessfully',
                'NOT_FOUND' => 'Currency not found',
            ]
        ];
    }
}