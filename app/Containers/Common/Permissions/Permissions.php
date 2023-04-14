<?php

namespace App\Containers\Common\Permissions;

class Permissions
{
    public static function permissions()
    {
        return [
            [
                'name' => 'Write Contact type',
                'slug' => 'write-contact_type',
                'description' => 'Can create, update and delete contact types'
            ]
        ];
    }
}