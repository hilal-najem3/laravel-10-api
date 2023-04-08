<?php

namespace App\Containers\Users\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Response\ResponseHelper;
use App\Requests\PaginationRequest;

use App\Containers\Common\Helpers\MessagesHelper;

use App\Containers\Users\Requests\UpdateUserContactDataRequest;
use App\Containers\Users\Requests\DeleteUserContactDataRequest;
use App\Containers\Users\Validators\UsersValidators;
use App\Containers\Users\Requests\UserArraysRequest;
use App\Containers\Users\Requests\CreateUserRequest;
use App\Containers\Users\Requests\UpdateUserRequest;
use App\Containers\Users\Helpers\UserHelper;

use App\Containers\Common\Helpers\ContactHelper;

use Exception;

use App\Containers\Common\Traits\PermissionControllersTrait;

class UsersController extends Controller
{
    use ResponseHelper, UsersValidators, PermissionControllersTrait;

    protected $messages = array();

    public function __construct()
    {
        $this->messages = MessagesHelper::messages();;
    }

    /**
     * Get all users
     * 
     * @param PaginationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function get(PaginationRequest $request)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['get-users'], $this->messages['USERS']['GET_ALLOWED_ERROR']);

            $data = UserHelper::getAll($request->get('pagination'));
            $info = [
                'meta' => $this->metaData($data),
                'users' => $data->data
            ];
            return $this->return_response(
                $this->success,
                $info,
                $this->messages['USERS']['GET']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['GET_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['GET_ERROR']
        );
    }

    /**
     * Get User By Id
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function id(int $id)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['get-users'], $this->messages['USERS']['GET_ALLOWED_ERROR']);
            $user = UserHelper::full($id);
            
            $info = [
                'user' => $user,
            ];

            return $this->return_response(
                $this->success,
                $info,
                $this->messages['USERS']['GET_ID']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['GET_ID_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['GET_ID_ERROR']
        );
    }

    /**
     * Create a new user
     * 
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateUserRequest $request)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['create-users'], $this->messages['USERS']['CREATE_USER_NOT_ALLOWED']);
            $data = $request->all();
            $user = UserHelper::create($data);

            return $this->return_response(
                $this->success,
                ['user' => $user],
                $this->messages['USERS']['CREATE_USER_SUCCESS']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['CREATE_USER_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['CREATE_USER_FAILED']
        );
    }

    /**
     * Update a user
     * 
     * @param UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, int $id)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['update-users'], $this->messages['USERS']['UPDATE_USER_NOT_ALLOWED']);
            $data = $request->all();
            $user = UserHelper::id($id);

            $this->crossAuthorization([$id]);

            $user = UserHelper::update($user, $data);

            return $this->return_response(
                $this->success,
                ['user' => $user],
                $this->messages['USERS']['UPDATE_USER_SUCCESS']
            );

        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['UPDATE_USER_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['UPDATE_USER_FAILED']
        );
    }

    /**
     * Update a user contact data
     * 
     * @param UpdateUserContactDataRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateContactData(UpdateUserContactDataRequest $request, int $id)
    {
        $this->messages = $this->messages();


        try {
            $this->allowedAction(['update-users'], $this->messages['USERS']['UPDATE_USER_NOT_ALLOWED']);

            $data = $request->all();
            $user = UserHelper::id($id);

            $this->crossAuthorization([$id]);

            foreach($data['contact'] as $contactData) {
                $data = [
                    'type_id' => $contactData['type_id'],
                    'value' => trim($contactData['value']),
                ];

                if(isset($contactData['id'])) {
                    // update the contact
                    $contact = ContactHelper::id($contactData['id']);
                    ContactHelper::updateContact($contact, $data, 'users', $user->id);
                } else {
                    // create a new contact
                    ContactHelper::createContact($data, 'users', $user->id);
                }
            }

            $user = UserHelper::full($user->id);
            return $this->return_response(
                $this->success,
                ['user' => $user],
                $this->messages['USERS']['UPDATE_USER_SUCCESS']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['UPDATE_USER_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['UPDATE_USER_FAILED']
        );
    }

    /**
     * Update a user contact data
     * 
     * @param DeleteUserContactDataRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteContactData(DeleteUserContactDataRequest $request, int $id)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['update-users'], $this->messages['USERS']['UPDATE_USER_NOT_ALLOWED']);

            $data = $request->all();
            $user = UserHelper::id($id);

            $this->crossAuthorization([$id]);

            return $this->return_response(
                $this->success,
                ['user' => $user],
                $this->messages['USERS']['UPDATE_USER_SUCCESS']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['UPDATE_USER_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['UPDATE_USER_FAILED']
        );
    }

    /**
     * Add Permissions
     * This function adds permissions to users
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addPermissionsToUser(Request $request)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['attach-permissions'], $this->messages['USERS']['ATTACH_PERMISSIONS_NOT_ALLOWED']);

            $data = $request->all();
            $this->permissions_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            foreach($data['permissions'] as $permissionId) {
                UserHelper::attachPermission($user, $permissionId);
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['ATTACH_PERMISSIONS']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['ATTACH_PERMISSIONS_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['ATTACH_PERMISSIONS_FAILED']
        );
    }

    /**
     * Remove Permissions
     * This function removes permissions to users
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function removePermissionsToUser(Request $request)
    {
        $this->messages = $this->messages();


        try {
            $this->allowedAction(['attach-permissions'], $this->messages['USERS']['ATTACH_PERMISSIONS_NOT_ALLOWED']);

            $data = $request->all();
            $this->permissions_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            foreach($data['permissions'] as $permissionId) {
                UserHelper::detachPermission($user, $permissionId);
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['DETACH_PERMISSIONS']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['DETACH_PERMISSIONS_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['DETACH_PERMISSIONS_FAILED']
        );
    }

    /**
     * Add Roles
     * This function adds roles to users
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function addRolesToUser(Request $request)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['attach-roles'], $this->messages['USERS']['ATTACH_ROLES_NOT_ALLOWED']);

            $data = $request->all();
            $this->roles_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            foreach($data['roles'] as $roleId) {
                UserHelper::attachRole($user, $roleId);
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['ATTACH_ROLES']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['ATTACH_ROLES_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['ATTACH_ROLES_FAILED']
        );
    }

    /**
     * Remove Roles
     * This function removes roles to users
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function removeRolesToUser(Request $request)
    {
        $this->messages = $this->messages();


        try {
            $this->allowedAction(['attach-roles'], $this->messages['USERS']['ATTACH_ROLES_NOT_ALLOWED']);

            $data = $request->all();
            $this->roles_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            foreach($data['roles'] as $roleId) {
                UserHelper::detachRole($user, $roleId);
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['DETACH_ROLES']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['DETACH_ROLES_FAILED'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['DETACH_ROLES_FAILED']
        );
    }

    /**
     * Deactivate Users
     * This function deactivates users
     * revokes his logged in tokens preventing him
     * from exercising his activities to this api
     * 
     * @param UserArraysRequest $request
     * @return \Illuminate\Http\Response
     */
    public function deactivateUsers(UserArraysRequest $request)
    {
        $this->messages = $this->messages();


        try {
            $this->allowedAction(['activate-user'], $this->messages['USERS']['ACTIVATE_DEACTIVATE_USER_NOT_ALLOWED']);
            $ids = $request->get('user_ids');
            
            $this->crossAuthorization($ids);

            foreach($ids as $id) {
                UserHelper::inActivate(UserHelper::id($id));
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['DEACTIVATE']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['DEACTIVATE_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response($this->bad_request, [], $this->messages['USERS']['DEACTIVATE_ERROR']);
    }

    /**
     * Activate User
     * This function activates users
     * allowing him to exercise his activities to this api
     * 
     * @param UserArraysRequest $request
     * @return \Illuminate\Http\Response
     */
    public function activateUsers(UserArraysRequest $request)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['activate-user'], $this->messages['USERS']['ACTIVATE_DEACTIVATE_USER_NOT_ALLOWED']);
            $ids = $request->get('user_ids');
            
            $this->crossAuthorization($ids);

            foreach($ids as $id) {
                UserHelper::activate(UserHelper::id($id));
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['ACTIVATE']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['ACTIVATE_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response($this->bad_request, [], $this->messages['USERS']['ACTIVATE_ERROR']);
    }

    /**
     * Delete Users
     * This function deletes users
     * revokes his logged in tokens preventing him
     * from exercising his activities to this api
     * and prevent users from restoring his profile.
     * 
     * To restore request the restore deleted route
     * 
     * @param UserArraysRequest $request
     * @return \Illuminate\Http\Response
     */
    public function deleteUsers(UserArraysRequest $request)
    {
        $this->messages = $this->messages();

        try {
            $this->allowedAction(['delete-user'], $this->messages['USERS']['DELETE_USER_NOT_ALLOWED']);
            $ids = $request->get('user_ids');
            
            $this->crossAuthorization($ids);

            foreach($ids as $id) {
                UserHelper::deleteUser(UserHelper::id($id));
            }

            return $this->return_response(
                $this->success,
                [],
                $this->messages['USERS']['DELETE_SUCCESSFUL']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['DELETE_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response($this->bad_request, [], $this->messages['USERS']['DELETE_ERROR']);
    }

    /**
     * This functions returns the profiles of the deleted users
     * Being deleted only by soft deletes
     *
     * @param PaginationRequest $request 
     * @return \Illuminate\Http\Response
     */
    public function getDeletedUsers(PaginationRequest $request)
    {

        try {
            $this->allowedAction(['get-deleted-users'], $this->messages['USERS']['GET_ERROR']);
            $data = UserHelper::getDeleted($request->get('pagination'));
            
            $info = [
                'meta' => $this->metaData($data),
                'users' => $data->data
            ];

            return $this->return_response(
                $this->success,
                $info,
                $this->messages['USERS']['GET']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['GET_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['GET_ERROR']
        );
    }

    /**
     * This functions returns the in activated users
     * 
     * @param PaginationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getInActiveUsers(PaginationRequest $request)
    {
        try {
            $this->allowedAction(['get-inactive-users'], $this->messages['USERS']['GET_ERROR']);
            $data = UserHelper::getInActivated($request->get('pagination'));
            
            $info = [
                'meta' => $this->metaData($data),
                'users' => $data->data
            ];

            return $this->return_response(
                $this->success,
                $info,
                $this->messages['USERS']['GET']
            );
        } catch (Exception $e) {
            return $this->return_response(
                $this->bad_request,
                [],
                $this->messages['USERS']['GET_ERROR'],
                $this->exception_message($e)
            );
        }

        return $this->return_response(
            $this->bad_request,
            [],
            $this->messages['USERS']['GET_ERROR']
        );
    }
}