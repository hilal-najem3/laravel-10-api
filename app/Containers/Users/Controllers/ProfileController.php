<?php

namespace App\Containers\Users\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Users\Requests\UpdateUserContactDataRequest;
use App\Containers\Users\Requests\DeleteUserContactDataRequest;
use App\Containers\Users\Requests\UpdateUserPasswordRequest;
use App\Containers\Users\Requests\UpdateUserPhotoRequest;
use App\Containers\Users\Requests\UpdateUserRequest;

use App\Containers\Users\Helpers\UserHelper;
use App\Containers\Auth\Helpers\UserAuthHelper;
use App\Containers\Common\Helpers\ContactHelper;

use Illuminate\Support\Facades\Auth;

use App\Exceptions\Common\NotAllowedException;
use Exception;

class ProfileController extends Controller
{
    use ResponseHelper;

    /**
     * Get logged in user profile
     * 
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        try {
            $info = [
                'user' => UserHelper::profile()
            ];

            return $this->response('PROFILE.GET', $info);
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'PROFILE.GET_ERROR', $e);
        }
        
        return $this->errorResponse($this->bad_request, 'PROFILE.GET_ERROR');
    }

    /**
     * Update logged in user profile
     * 
     * @param  UpdateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request)
    {
        try {
            $data = $request->all();

            $user = Auth::user();
            $updateUser = UserHelper::update($user, $data);

            $data = [
                'user' => UserHelper::profile()
            ];

            return $this->response(
                'PROFILE.UPDATE_SUCCESS',
                $data
            );
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'PROFILE.UPDATE_ERROR', $e);
        }

        return $this->errorResponse($this->bad_request, 'PROFILE.UPDATE_ERROR');
    }

    /**
     * Update logged in user password
     * 
     * @param  UpdateUserPasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(UpdateUserPasswordRequest $request)
    {
        try {
            $data = $request->all();

            $user = Auth::user();
            $updated = UserHelper::updatePassword($user, $data);

            $token = UserAuthHelper::logoutFromAllAndRefreshToken($user);

            if($updated) {
                return $this->response(
                    'PROFILE.PASSWORD',
                    [ 'token' => $token ]
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'PROFILE.PASSWORD_ERROR', $e);
        }
        return $this->errorResponse($this->bad_request, 'PROFILE.PASSWORD_ERROR');
    }

    /**
     * Uploads profile photo
     * 
     * @param  UpdateUserPhotoRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updatePhoto(UpdateUserPhotoRequest $request)
    {
        try {
            $user = Auth::user();
            $photo = $request->file('photo');

            if($photo == null) {
                UserHelper::updateProfilePhoto($user, $photo); // this should delete the user's photo
            } else {
                $image = UserHelper::updateProfilePhoto($user, $photo, $request->file('photo')->getSize());
            }

            return $this->response(
                'PROFILE.UPDATE_SUCCESS',
                ['user' => UserHelper::profile()]
            );
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'PROFILE.UPDATE_ERROR', $e);
        }
        return $this->errorResponse($this->bad_request, 'PROFILE.UPDATE_ERROR');
    }

    /**
     * Uploads profile photo
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete()
    {
        try {
            $user = Auth::user();
            UserHelper::deleteUser($user, true);

            return $this->response('PROFILE.DELETE_SUCCESS');
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'PROFILE.DELETE_ERROR', $e);
        }
        return $this->errorResponse($this->bad_request, 'PROFILE.DELETE_ERROR');
    }

    /**
     * Uploads profile contact info
     * 
     * @param  UpdateUserContactDataRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateContact(UpdateUserContactDataRequest $request)
    {
        try {
            $data = $request->all();
            $user = $request->user();

            foreach($data['contact'] as $contactData) {
                $data = [
                    'type_id' => $contactData['type_id'],
                    'value' => trim($contactData['value']),
                ];

                if(isset($contactData['id'])) {
                    // update the contact
                    $contact = ContactHelper::id($contactData['id']);
                    UserHelper::canSubmitContact($user, $data, $contact); // this will throw exception if submit is not allowed
                    ContactHelper::updateContact($contact, $data, 'users', $user->id);
                } else {
                    UserHelper::canSubmitContact($user, $data); // this will throw exception if submit is not allowed
                    // create a new contact
                    ContactHelper::createContact($data, 'users', $user->id);
                }
            }

            $user = UserHelper::full($user->id);
            return $this->response('USERS.USER_CONTACT_DATA_UPDATED', ['user' => $user]);
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'USERS.USER_CONTACT_DATA_UPDATE_FAILED', $e);
        }
        return $this->errorResponse($this->bad_request, 'USERS.USER_CONTACT_DATA_UPDATE_FAILED');
    }

    /**
     * Update a profile contact data
     * 
     * @param DeleteUserContactDataRequest $request
     * @return \Illuminate\Http\Response
     */
    public function deleteContactData(DeleteUserContactDataRequest $request)
    {
        try {
            $user = $request->user();
            $contactIds = $request->all()['contact'];

            foreach ($contactIds as $contactId) {
                if(!$user->contact()->where('id', $contactId)->count()) {
                    throw new NotAllowedException('', 'USERS.USER_CONTACT_ID_IS_DIFFERENT');
                }
                ContactHelper::deleteContact($contactId);
            }

            $user = UserHelper::full($user->id);
            return $this->response('USERS.USER_CONTACT_DATA_DELETED', ['user' => $user]);
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'USERS.USER_CONTACT_DATA_DELETE_FAILED', $e);
        }
        return $this->errorResponse($this->bad_request, 'USERS.USER_CONTACT_DATA_DELETE_FAILED');
    }
}
