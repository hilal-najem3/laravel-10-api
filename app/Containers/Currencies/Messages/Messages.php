<?php

namespace App\Containers\Currencies\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'CURRENCY' => [
                'GET' => 'Currency data loaded successfully',
                'NOT_FOUND' => 'Currency not found',
            ],
            'CONVERSION_HISTORY' => [
                'NAME' => 'Conversion history'
            ]
        ];
    }
}