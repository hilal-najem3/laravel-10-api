<?php

namespace App\Containers\Common\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'DATA' => [
                'TYPE' => 'Data type',
                'DATA' => 'Data',
                'EXCEPTION' => 'Data',
                'VALUE' => 'Value'
            ],
            'REGIONS' => [
                'ALL' => 'Countries and region types loaded successfully',
                'ALL_FAILED' => 'Countries and region types load failed'
            ],
            'CONTACT_TYPES' => [
                'GET' => 'Contact type/s loaded successfully',
                'GET_ERROR' => 'Contact types loaded successfully',
                'CREATE_SUCCESSFUL' => 'Contact type created successfully',
                'CREATE_FAILED' => 'Contact type creation failed',
                'DUPLICATE_NAME' => 'Contact type already exists',
            ],
            'CONTACT' => [
                'CONTACT_EXCEPTION' => 'Contact',
                "CONTACT_TYPE_EXCEPTION" => 'Contact type'
            ]
        ];
    }
}
