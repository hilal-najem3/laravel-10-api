<?php

namespace App\Containers\Common\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'DATA' => [
                'EXCEPTION' => 'Data'
            ],
            'REGIONS' => [
                'ALL' => 'Countries and region types loaded successfully',
                'ALL_FAILED' => 'Countries and region types load failed'
            ],
            'CONTACT' => [
                'TYPES' => 'Contact types loaded successfully',
                'TYPES_ERROR' => 'Contact types loaded successfully',
                'CONTACT_EXCEPTION' => 'Contact',
                "CONTACT_TYPE_EXCEPTION" => 'Contact type'
            ]
        ];
    }
}
