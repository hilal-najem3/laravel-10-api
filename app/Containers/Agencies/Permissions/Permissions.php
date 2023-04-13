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
            ],
            [
                'name' => 'Write Agency',
                'slug' => 'write-agency',
                'description' => 'Can create, update and delete agencies'
            ],
            [
                'name' => 'Get Agency',
                'slug' => 'get-agency',
                'description' => 'Can get agency'
            ]
        ];
    }
}