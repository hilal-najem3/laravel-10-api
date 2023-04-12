<?php

namespace App\Containers\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Response\ResponseHelper;
use App\Containers\Auth\Helpers\UserAuthHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Containers\Auth\Requests\LoginUserRequest;
use App\Containers\Auth\Requests\ForgotPasswordRequest;
use App\Containers\Auth\Requests\ResetPasswordPasswordRequest;
use Exception;

class PassportAuthController extends Controller
{
    use ResponseHelper;

    protected $messages = array();

    /**
     * Login user
     * 
     * @param  LoginUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function login(LoginUserRequest $request)
    {
        try {
            $user_data = $request->all();

            $info = UserAuthHelper::login($user_data);
            
            if($info == null) {
                return $this->return_response($this->not_found, [], 'LOGIN_FAILED');
            }
        
            return $this->response(
               'LOGIN_SUCCESS',
               $info
            );
        } catch (Exception $e) {
            return $this->return_response($this->bad_request, [], 'LOGIN_FAILED', $e);
        }

        return $this->return_response($this->bad_request, [], 'LOGIN_FAILED');
    }

    /**
     * Logout user
     * 
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        try {
            $revoked = UserAuthHelper::logout($request->user());
            
            if($revoked) {
                return $this->response(
                    'LOGOUT_SUCCESS'
                );
            }
        } catch (Exception $e) {
            return $this->errorResponse('LOGOUT_FAILED', $e);
        }

        return $this->errorResponse('LOGOUT_FAILED');
    }

    /**
     * Forgot password
     * Send a reset password email to user
     * 
     * @param  ForgotPasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $request->all();

        $response = Password::sendResetLink($data);
        
        $messageKey = $response == Password::RESET_LINK_SENT ? 
        'FORGOT_EMAIL_SUCCESS' : 'FORGOT_EMAIL_FAIL';

        $status = $response == Password::RESET_LINK_SENT ? $this->success : $this->bad_request;

        return $this->return_response(
            $status,
            [],
            $messageKey
        );
    }

    /**
     * Reset password
     * Reset password for a user
     * 
     * @param  ResetPasswordPasswordRequest $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(ResetPasswordPasswordRequest $request)
    {
        $data = $request->all();

        $reset_password_status = Password::reset($data, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        $messageKey = 'RESET_PASSWORD_FAIL';
        $status = $this->bad_request;

        switch($reset_password_status) {
            case Password::INVALID_TOKEN: {
                $status = $this->unauthorized;
                break;
            }
            case Password::INVALID_USER: {
                $status = $this->bad_request;
                break;
            }

            case Password::PASSWORD_RESET: {
                $message = 'RESET_PASSWORD_SUCCESS';
                $status = $this->success;
                break;
            }
            default: {
                $status = $this->bad_request;
                break;
            }
        }

        return $this->return_response(
            $status,
            [],
            $messageKey
        );
    }
}
