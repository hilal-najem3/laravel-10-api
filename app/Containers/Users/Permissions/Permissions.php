<?php

namespace App\Containers\Users\Permissions;

class Permissions
{
    public static function permissions()
    {
        return [
            [
                'name' => 'Get Users',
                'slug' => 'get-users',
                'description' => 'Get all users',
                'roles' => [2]
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create-users',
                'description' => 'Create a new user with full data',
                'roles' => [2]
            ],
            [
                'name' => 'Update Users',
                'slug' => 'update-users',
                'description' => 'Update users with full data',
                'roles' => [2]
            ],
            [
                'name' => 'Attach Roles',
                'slug' => 'attach-roles',
                'description' => 'Add and remove roles for user'
            ],
            [
                'name' => 'Attach Permissions',
                'slug' => 'attach-permissions',
                'description' => 'Add and remove permissions for user'
            ],
            [
                'name' => 'Activate/Deactivate User',
                'slug' => 'activate-user',
                'description' => 'Activate or deactivate a user\'s account'
            ],
            [
                'name' => 'Delete User',
                'slug' => 'delete-user',
                'description' => 'Delete a user\'s account',
                'roles' => [2]
            ],
            [
                'name' => 'Get deleted users',
                'slug' => 'get-deleted-users',
                'description' => 'Get the list of deleted users',
                'roles' => [2]
            ],
            [
                'name' => 'Get inactive users',
                'slug' => 'get-inactive-users',
                'description' => 'Get the list of inactive users',
                'roles' => [2]
            ],
            [
                'name' => 'Add/Remove user agency admin',
                'slug' => 'write-user-agency-admin',
                'description' => 'Can add or remove user from being an agency admin',
                'roles' => [2]
            ]
        ];
    }
}