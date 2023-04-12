<?php

namespace App\Containers\Agencies\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Agencies\Helpers\AgencyTypesHelper as Helper;

use App\Containers\Agencies\Requests\CreateAgencyTypeRequest;
use App\Containers\Agencies\Requests\UpdateAgencyTypeRequest;

use Exception;

class AgencyTypesController extends Controller
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
            $this->allowedAction(['get-agency_type'], 'AGENCY_TYPES.NOT_FOUND');

            $data = [];

            if($id == null) {
                $agencyTypes = Helper::all();
                $data = [
                    'agency_types' => $agencyTypes
                ];
            }

            if($id != null) {
                $agencyType = Helper::id($id);
                $data = [
                    'agency_type' => $agencyType
                ];
            }

            return $this->response('AGENCY_TYPES.CREATE_SUCCESSFUL', $data);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_TYPES.NOT_FOUND', $e);
        }

        return $this->errorResponse('AGENCY_TYPES.NOT_FOUND');
    }

    /**
     * Create a new agency type
     * 
     * @param CreateAgencyTypeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateAgencyTypeRequest $request)
    {
        try {
            $this->allowedAction(['write-agency_type'], 'AGENCY_TYPES.CREATE_FAILED');

            $data = $request->all();
            $agencyType = Helper::create($data);

            return $this->response('AGENCY_TYPES.CREATE_SUCCESSFUL', ['agency_type' => $agencyType]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_TYPES.CREATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY_TYPES.CREATE_FAILED');
    }

    /**
     * Create a new agency type
     * 
     * @param UpdateAgencyTypeRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgencyTypeRequest $request, $id)
    {
        try {
            $this->allowedAction(['write-agency_type'], 'AGENCY_TYPES.UPDATE_FAILED');

            $data = $request->all();
            $agencyType = Helper::id($id);
            $agencyType = Helper::update($agencyType, $data);

            return $this->response('AGENCY_TYPES.UPDATE_SUCCESSFUL', ['agency' => $agencyType]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_TYPES.UPDATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY_TYPES.UPDATE_FAILED');
    }
}