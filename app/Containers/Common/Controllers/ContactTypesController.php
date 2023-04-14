<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Requests\CreateContactTypeRequest;
use App\Containers\Common\Requests\UpdateContactTypeRequest;

use App\Containers\Common\Helpers\ContactTypesHelper as Helper;

use App\Containers\Common\Models\Contact;

use Exception;

class ContactTypesController extends Controller
{
    use ResponseHelper, PermissionControllersTrait;

    /**
     * Get all contacts
     * by with contact type id
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getContactsByContactTypeId(int $id = null)
    {
        try {
            $info = [];

            $contact_type = Helper::id($id);
            $contacts = $contact_type->contacts()->get()
            ->each(function(Contact $contact) {
                $contact = $contact->load(['users']);
            });

            $info = [
                'contact_type' => $contact_type,
                'contacts' => $contacts
            ];

            return $this->response(
                'CONTACT_TYPES.GET_CONTACTS',
                $info
            );
        } catch (Exception $e) {
            return $this->errorResponse('CONTACT_TYPES.GET_CONTACTS_ERROR', $e);
        }

        return $this->errorResponse('CONTACT_TYPES.GET_CONTACTS_ERROR');
    }

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
     * Create a new contact type
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

    /**
     * Update a contact type
     * 
     * @param UpdateContactTypeRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateContactTypeRequest $request, int $id)
    {
        try {
            $this->allowedAction(['write-contact_type'], 'CONTACT_TYPES.UPDATE_FAIL');
            $data = $request->all();
            
            $contact_type = Helper::id($id);
            $contact_type = Helper::update($contact_type, $data);

            return $this->response('CONTACT_TYPES.UPDATE_SUCCESSFUL', ['contact_type' => $contact_type]);

        } catch (Exception $e) {
            return $this->errorResponse('CONTACT_TYPES.UPDATE_FAIL', $e);
        }

        return $this->errorResponse('CONTACT_TYPES.UPDATE_FAIL');
    }

    /**
     * Delete a contact type
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function delete(int $id)
    {
        try {
            $this->allowedAction(['write-contact_type'], 'CONTACT_TYPES.DELETE_FAIL');
            
            $contact_type = Helper::id($id);
            $contact_type = Helper::delete($contact_type);

            return $this->response('CONTACT_TYPES.DELETE_SUCCESSFUL', ['contact_type' => $contact_type]);

        } catch (Exception $e) {
            return $this->errorResponse('CONTACT_TYPES.DELETE_FAIL', $e);
        }

        return $this->errorResponse('CONTACT_TYPES.DELETE_FAIL');
    }
}