<?php

namespace App\Containers\Agencies\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Agencies\Helpers\AgencyHelper as Helper;

use App\Containers\Agencies\Requests\UpdateAgencyLogoRequest;
use App\Containers\Agencies\Requests\CreateAgencyRequest;
use App\Containers\Agencies\Requests\UpdateAgencyRequest;

use Exception;

class AgencyController extends Controller
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
            $this->allowedAction(['get-agency'], 'AGENCY.NOT_FOUND');

            $data = [];

            if($id == null) {
                $agencies = Helper::all();
                $data = [
                    'agencies' => $agencies
                ];
            }

            if($id != null) {
                $agency = Helper::full($id);
                $data = [
                    'agency' => $agency
                ];
            }

            return $this->response('AGENCY.GET', $data);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY.NOT_FOUND', $e);
        }

        return $this->errorResponse('AGENCY.NOT_FOUND');
    }

    /**
     * Create a new agency type
     * 
     * @param CreateAgencyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateAgencyRequest $request)
    {
        try {
            $this->allowedAction(['write-agency'], 'AGENCY.CREATE_FAILED');

            $data = $request->all();
            $agency = Helper::create($data);

            return $this->response('AGENCY.CREATE_SUCCESSFUL', ['agency' => $agency]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY.CREATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY.CREATE_FAILED');
    }

    /**
     * Update an agency
     * 
     * @param UpdateAgencyRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAgencyRequest $request, $id)
    {
        try {
            $this->allowedAction(['write-agency'], 'AGENCY.UPDATE_FAILED');

            $data = $request->all();
            $agency = Helper::id($id);
            $agency = Helper::update($agency, $data);

            return $this->response('AGENCY.UPDATE_SUCCESSFUL', ['agency' => $agency]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY.UPDATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY.UPDATE_FAILED');
    }

    /**
     * Update an agency
     * 
     * @param UpdateAgencyLogoRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateLogo(UpdateAgencyLogoRequest $request, $id)
    {
        try {
            $this->allowedAction(['write-agency'], 'AGENCY.UPDATE_FAILED');
            $image = $request->file('logo');
            $agency = Helper::id($id);

            if($image == null) {
                Helper::updateLogo($agency, null);
            } else {
                $image = Helper::updateLogo($agency, $image, $request->file('logo')->getSize());
            }

            return $this->response('AGENCY.UPDATE_SUCCESSFUL', ['agency' => $agency]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY.UPDATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY.UPDATE_FAILED');
    }
}