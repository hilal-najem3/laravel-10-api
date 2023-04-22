<?php

namespace App\Containers\Plans\Permissions;

class Permissions
{
    public static function permissions()
    {
        return [
            [
                'name' => 'Get plans',
                'slug' => 'get-plans',
                'description' => 'Can get payment plans for agencies',
                'roles' => [2]
            ],
            [
                'name' => 'Write plans',
                'slug' => 'write-plans',
                'description' => 'Can get create, update and delete plans for agencies',
                'roles' => [2]
            ],
            [
                'name' => 'Get agency plans',
                'slug' => 'get-agency-plans',
                'description' => 'Can get payment plans connected with an agency',
                'roles' => [2]
            ],
            [
                'name' => 'Write agency plans',
                'slug' => 'write-agency-plans',
                'description' => 'Can get create, update and delete plans connected with an agency',
                'roles' => [2]
            ]
        ];
    }
}