<?php

namespace App\Containers\Plans\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Plans\Requests\CreatePlanRequest;
use App\Containers\Plans\Requests\UpdatePlanRequest;

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

    /**
     * Create a new plan
     * 
     * @param CreatePlanRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreatePlanRequest $request)
    {
        try {
            $this->allowedAction(['write-plans'], 'PLANS.CREATE_FAILED');

            $data = $request->all();
            $data = Helper::trim($data);
            $plan = Helper::baseCreate($data);

            return $this->response('PLANS.CREATE_SUCCESSFUL', ['plan' => $plan]);

        } catch (Exception $e) {
            return $this->errorResponse('PLANS.CREATE_FAILED', $e);
        }

        return $this->errorResponse('PLANS.CREATE_FAILED');
    }

    /**
     * Update a plan
     * 
     * @param UpdatePlanRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlanRequest $request, int $id)
    {
        try {
            $this->allowedAction(['write-plans'], 'PLANS.UPDATE_FAILED');

            $data = $request->all();
            $data = Helper::trim($data);
            $plan = Helper::id($id);
            Helper::checkUpdateData($plan, $data);
            $plan = Helper::baseUpdate($plan, $data);

            return $this->response('PLANS.UPDATE_SUCCESSFUL', ['plan' => $plan]);

        } catch (Exception $e) {
            return $this->errorResponse('PLANS.UPDATE_FAILED', $e);
        }

        return $this->errorResponse('PLANS.UPDATE_FAILED');
    }
}