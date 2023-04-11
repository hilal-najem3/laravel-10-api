<?php

namespace App\Containers\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Auth\Helpers\UserAuthHelper;
use App\Containers\Users\Requests\UpdateUserPasswordRequest;
use App\Containers\Users\Requests\UpdateUserPhotoRequest;
use App\Containers\Users\Requests\UpdateUserRequest;
use App\Containers\Users\Helpers\UserHelper;

use Illuminate\Support\Facades\Auth;
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
}
