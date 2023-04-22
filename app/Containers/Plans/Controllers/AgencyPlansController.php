<?php

namespace App\Containers\Plans\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Plans\Requests\AgencyPlansRequest;

use App\Containers\Plans\Helpers\AgencyPlansHelper as Helper;

use Exception;

class AgencyPlansController extends Controller
{
    use ResponseHelper, PermissionControllersTrait;

    /**
     * Get all agency plans
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(int $id = null)
    {
        try {
            $this->allowedAction(['get-agency-plans'], 'AGENCY_PLANS.NOT_FOUND');
            $data = [];
            if($id == null) {
                $plans = Helper::all();
                $data = [
                    'agency_plans' => $plans
                ];
            }
            if($id != null) {
                $plan = Helper::id($id);
                $data = [
                    'agency_plan' => $plan
                ];
            }
            return $this->response('AGENCY_PLANS.GET', $data);
        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_PLANS.NOT_FOUND', $e);
        }

        return $this->errorResponse('AGENCY_PLANS.NOT_FOUND');
    }

    /**
     * Create a new plan
     * 
     * @param AgencyPlansRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(AgencyPlansRequest $request)
    {
        try {
            $this->allowedAction(['write-agency-plans'], 'AGENCY_PLANS.CREATE_FAILED');

            $data = $request->all();
            $data = Helper::trimAndSetup($data);
            Helper::checkUploadedData(null, $data, true);
            $plan = Helper::baseCreate($data);

            return $this->response('AGENCY_PLANS.CREATE_SUCCESSFUL', ['agency_plan' => $plan]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_PLANS.CREATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY_PLANS.CREATE_FAILED');
    }

    /**
     * Update a plan
     * 
     * @param AgencyPlansRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgencyPlansRequest $request, int $id)
    {
        try {
            $this->allowedAction(['write-agency-plans'], 'AGENCY_PLANS.UPDATE_FAILED');

            $data = $request->all();
            $data = Helper::trimAndSetup($data);
            $plan = Helper::id($id);
            Helper::checkUploadedData($plan, $data);
            $plan = Helper::baseUpdate($plan, $data);

            return $this->response('AGENCY_PLANS.UPDATE_SUCCESSFUL', ['agency_plan' => $plan]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_PLANS.UPDATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY_PLANS.UPDATE_FAILED');
    }
}