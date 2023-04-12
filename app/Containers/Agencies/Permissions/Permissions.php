<?php

namespace App\Containers\Agencies\Permissions;

class Permissions
{
    public static function permissions()
    {
        return [
            [
                'name' => 'Write Agency type',
                'slug' => 'write-agency_type',
                'description' => 'Can create, update and delete agency types'
            ],
            [
                'name' => 'Get Agency type',
                'slug' => 'get-agency_type',
                'description' => 'Can get agency type'
            ]
        ];
    }
}