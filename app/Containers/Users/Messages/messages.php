<?php

namespace App\Containers\Users\Messages;

class Messages
{
    public static function messages()
    {
        return [
            'PROFILE' => [
                'GET' => 'Returned Profile',
                'GET_ERROR' => 'Unable to get user profile',
                'EXCEPTION' => 'User profile',
                'UPDATE_ERROR' => 'User profile update failed',
                'UPDATE_SUCCESS' => 'User profile updated successfully',
                'CREATE_ERROR' => 'Create user failed',
                'CREATE_SUCCESS' => 'User created successfully',
                'PASSWORD' => 'Password updated successfully',
                'PASSWORD_ERROR' => 'Password updated failed. The password should have at least 6 characters, 1 capital letter, 1 number and 1 special character',
                'OLD_PASSWORD_ERROR' => 'Old password is incorrect',
                'OLD_PASSWORD_ERROR_EQUAL_NEW' => 'Password shouldn\'t be the same as the old one',
                'DELETE_SUCCESS' => 'Profile deleted successfully. You can restore your account if logged again within 60 days from now',
                'DELETE_ERROR' => 'Profile delete failed'
            ],
            'USERS' => [
                'USER' => 'User',
                'GET' => 'Users found',
                'GET_ALLOWED_ERROR' => 'get users',
                'GET_ERROR' => 'Unable to get users',
                'GET_ID' => 'User found',
                'ATTACH_PERMISSIONS' => 'Permissions added successfully',
                'ATTACH_PERMISSIONS_FAILED' => 'Attach permissions failed',
                'ATTACH_PERMISSIONS_NOT_ALLOWED' => 'attach/detach permissions',
                'DETACH_PERMISSIONS' => 'Permissions removed successfully',
                'DETACH_PERMISSIONS_FAILED' => 'Detach permissions failed',
                'ATTACH_ROLES' => 'Roles added successfully',
                'ATTACH_ROLES_FAILED' => 'Roles attach failed',
                'DETACH_ROLES' => 'Roles removed successfully',
                'DETACH_ROLES_FAILED' => 'Detach roles failed',
                'ATTACH_ROLES_NOT_ALLOWED'  => 'attach/detach roles',
                'ACTIVATE_DEACTIVATE_USER_NOT_ALLOWED' => 'activate/deactivate users',
                'CROSS_AUTH_ERROR' => 'edit a user with role hierarchy equal or greater than you',
                'DEACTIVATE' => 'Deactivation was successful',
                'DEACTIVATE_ERROR' => 'Deactivation failed',
                'ACTIVATE' => 'Activation was successful',
                'ACTIVATE_ERROR' => 'Activation failed',
                'DELETE_USER_NOT_ALLOWED' => 'delete users',
                'DELETE_SUCCESSFUL' => 'Delete was successful',
                'DELETE_ERROR' => 'Delete failed',
                'CREATE_USER_NOT_ALLOWED' => 'create users',
                'CREATE_USER_SUCCESS' => 'User created successfully',
                'CREATE_USER_FAILED' => 'User create failed',
                'UPDATE_USER_NOT_ALLOWED' => 'update users',
                'UPDATE_USER_SUCCESS' => 'User updated successfully',
                'UPDATE_USER_FAILED' => 'User update failed',
                'USER_CONTACT_DATA_UPDATED' => 'User contact data updated successfully',
                'USER_CONTACT_DATA_UPDATE_FAILED' => 'User contact data update failed',
                'USER_CONTACT_DATA_DELETED' => 'User contact data deleted successfully',
                'USER_CONTACT_DATA_DELETE_FAILED' => 'User contact data delete failed',
                'USER_CONTACT_ID_IS_DIFFERENT' => 'You are not allowed to update contact information for another entity associating him with this user id',
                'USER_CONTACT_VALUE_IS_USED' => 'You are not allowed to update or create contact information that already exists'
            ],
            'EMAIL_EXISTS' => 'Email already exists'
        ];
    }
}
