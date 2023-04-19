<?php

namespace App\Containers\Agencies\Traits;

use App\Exceptions\Common\NotAllowedException;
use App\Containers\Agencies\Models\Agency;
use Illuminate\Support\Facades\Auth;

trait UserAgencyPermissionsTrait
{
    /**
     * This function checks if authenticated user 
     * is allowed to update data for this agency
     * 
     * @param Agency $agency
     * @param string $messageKey
     * @throw NotAllowedException
     */
    public function allowAgencyUpdate(Agency $agency, string $messageKey = null)
    {
        $user = Auth::user();
        $isGeneralAdmin = $user->isGeneralAdmin();
        $isAgencyAdmin = $user->isAgencyAdmin();
        if(!$isGeneralAdmin && $isAgencyAdmin) {
            $agencyReturn = $user->agencies()->where('id', $agency->id)->first();
            if($agencyReturn == null) {
                throw new NotAllowedException('', $messageKey ? $messageKey : 'AGENCY_ADMIN.NOT_ALLOWED');
            }
        }
    }
}