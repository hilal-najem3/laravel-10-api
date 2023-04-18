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
                'UPDATE_SUCCESSFUL' => 'Contact type updates successfully',
                'UPDATE_FAIL' => 'Contact type update failed',
                'DUPLICATE_NAME' => 'Contact type already exists',
                'NAME' => 'Contact type',
                'NOT_FOUND' => 'Contact type',
                'HAS_CONTACTS' => 'Contact type delete failed, because it has many contacts associated with it',
                'DELETE_FAIL' => 'Contact type delete failed',
                'DELETE_SUCCESSFUL' => 'Contact type deletes successfully',
                'GET_CONTACTS' => 'Contacts associated contact type loaded successfully',
                'GET_CONTACTS_ERROR' => 'Contacts for contact type not found'
            ],
            'CONTACT' => [
                'CONTACT_EXCEPTION' => 'Contact',
                'NOT_FOUND' => 'Contact',
            ]
        ];
    }
}
