<?php

namespace App\Containers\Users\Helpers;

use Illuminate\Support\Facades\DB;

use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\NotAllowedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use Exception;

use App\Containers\Users\Helpers\UserRolesHelper;

use App\Containers\Agencies\Models\Agency;
use App\Models\User;

class UserAgencyHelper
{
    /**
     * Receives a user and an agency
     * This function sets this user as an agency admin
     * 
     * @param User $user
     * @param Agency $agency
     * @return boolean | UpdateFailedException
     */
    public static function addUserAsAnAgencyAdmin(User $user, Agency $agency)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $highestRole = UserRolesHelper::getHighestRole($user->roles()->get());
            if($highestRole->id > 3) {
                $ex = 'NotAllowedException';
                // He is a normal user cause admins role levels are 1, 2 and 3
                throw new NotAllowedException('', 'USER_AGENCY_ADMIN_LOW_LEVEL_FAIL');
            } 
            $user->agencies()->attach($agency);
            DB::commit();
            return true;
        } catch(Exception $e) {
            DB::rollBack();
            $ex == 'NotAllowedException' ?
            throw $e :
            throw new UpdateFailedException('', 'USER_AGENCY_ADMIN_FAIL');
        }
    }

    /**
     * Receives a user and an agency
     * This function revokes this user as an agency admin
     * 
     * @param User $user
     * @param Agency $agency
     * @return boolean | UpdateFailedException
     */
    public static function revokeUserAsAnAgencyAdmin(User $user, Agency $agency)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $highestRole = UserRolesHelper::getHighestRole($user->roles()->get());
            if($highestRole->id > 3) {
                $ex = 'NotAllowedException';
                // He is a normal user cause admins role levels are 1, 2 and 3
                throw new NotAllowedException('', 'USER_AGENCY_ADMIN_LOW_LEVEL_FAIL');
            } 
            $user->agencies()->detach($agency);
            DB::commit();
            return true;
        } catch(Exception $e) {
            DB::rollBack();
            $ex == 'NotAllowedException' ?
            throw $e :
            throw new UpdateFailedException('', 'USER_AGENCY_ADMIN_FAIL');
        }
    }
}