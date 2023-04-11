<?php

namespace App\Containers\Common\Traits;

use App\Containers\Users\Helpers\CrossAuthorizationHelper;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\Common\NotAllowedException;

trait PermissionControllersTrait
{
    // This trait calls for the cross authentication helper
    // It is user by the controllers to determine if a user is allowed to
    // edit the information of another user
    // Or it calls for the permission of the user to do a certain action and returns back to the FE the appropriate
    // response

    /**
     * This function calls upon the CrossAuthorizationHelper
     * to determine if $userDoingTheAction is allowed to edit data
     * for users with ids in array: $idsOfUsersAffectedByTheAction
     * 
     * @param array $idsOfUsersAffectedByTheAction
     */
    public function crossAuthorization(
        array $idsOfUsersAffectedByTheAction
        )
    {
        $userDoingTheAction = Auth::user();
        $crossAuth = CrossAuthorizationHelper::crossAuthorized($userDoingTheAction, $idsOfUsersAffectedByTheAction);
        if(!$crossAuth) {
            throw new NotAllowedException('USERS.CROSS_AUTH_ERROR');
        }
    }

    /**
     * This function checks if the user authenticated is allowed to perform the action
     * It returns the appropriate response otherwise
     * 
     * @param array $permissions
     * @param string $message
     */
    public function allowedAction(array $permissions, string $messageKey)
    {
        foreach($permissions as $permission) {
            $user = Auth::user();
            $allowed = $user->allowedTo($permission);
            if (!$allowed) {
                throw new NotAllowedException($messageKey);
            }
        }
    }
}