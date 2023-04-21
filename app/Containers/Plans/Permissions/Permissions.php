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
            ]
        ];
    }
}