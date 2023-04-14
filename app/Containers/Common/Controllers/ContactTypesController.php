<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Requests\CreateContactTypeRequest;

use App\Containers\Common\Helpers\ContactTypesHelper as Helper;

use Exception;

class ContactTypesController extends Controller
{
    use ResponseHelper;

    /**
     * Get all contact types
     * or with id
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(int $id = null)
    {
        try {
            $info = [];

            if($id != null) {
                $type = Helper::id($id);
                $info = ['contact_type' => $type];
            } else {
                $types = Helper::all();
                $info = ['contact_types' => $types];
            }

            return $this->response(
                'CONTACT_TYPES.GET',
                $info
            );
        } catch (Exception $e) {
            return $this->errorResponse('CONTACT_TYPES.GET_ERROR', $e);
        }

        return $this->errorResponse('CONTACT_TYPES.GET_ERROR');
    }

    /**
     * Create a new agency type
     * 
     * @param CreateContactTypeRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateContactTypeRequest $request)
    {
        try {
            $this->allowedAction(['write-contact_type'], 'CONTACT_TYPES.CREATE_FAILED');

            $data = $request->all();
            $contact_type = Helper::create($data);

            return $this->response('CONTACT_TYPES.CREATE_SUCCESSFUL', ['contact_type' => $contact_type]);

        } catch (Exception $e) {
            return $this->errorResponse('CONTACT_TYPES.CREATE_FAILED', $e);
        }

        return $this->errorResponse('CONTACT_TYPES.CREATE_FAILED');
    }
}