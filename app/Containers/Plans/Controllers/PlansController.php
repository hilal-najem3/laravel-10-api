<?php

namespace App\Containers\Plans\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Plans\Helpers\PlansHelper as Helper;

use Exception;

class PlansController extends Controller
{
    use ResponseHelper, PermissionControllersTrait;

    /**
     * Get all agency types
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(int $id = null)
    {
        try {
            $this->allowedAction(['get-plans'], 'PLANS.NOT_FOUND');
            $data = [];
            if($id == null) {
                $plans = Helper::all();
                $data = [
                    'plans' => $plans
                ];
            }
            if($id != null) {
                $plan = Helper::id($id);
                $data = [
                    'plan' => $plan
                ];
            }
            return $this->response('PLANS.GET', $data);
        } catch (Exception $e) {
            return $this->errorResponse('PLANS.NOT_FOUND', $e);
        }

        return $this->errorResponse('PLANS.NOT_FOUND');
    }
}