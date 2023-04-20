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
                'description' => 'Can create, update and delete agency types',
                'roles' => [2]
            ],
            [
                'name' => 'Get Agency type',
                'slug' => 'get-agency_type',
                'description' => 'Can get agency type',
                'roles' => [2]
            ],
            [
                'name' => 'Write Agency',
                'slug' => 'write-agency',
                'description' => 'Can create, update and delete agencies',
                'roles' => [2]
            ],
            [
                'name' => 'Get Agency',
                'slug' => 'get-agency',
                'description' => 'Can get agency',
                'roles' => [2]
            ],
            [
                'name' => 'Write Agency/Currency Data',
                'slug' => 'write-agency-currency',
                'description' => 'Can create, update and delete agency\'s currency information',
                'roles' => [2, 3]
            ],
            [
                'name' => 'Write Agency/Currency Conversions data',
                'slug' => 'write-agency-currency-conversion',
                'description' => 'Can create, update and delete agency\'s currency conversions information',
                'roles' => [2, 3]
            ],
            [
                'name' => 'CRUD on agency conversions history info',
                'slug' => 'crud-agency-conversions-history',
                'description' => 'Can create, read, update and delete agency\'s currency conversions history information',
                'roles' => [2, 3]
            ]
        ];
    }
}