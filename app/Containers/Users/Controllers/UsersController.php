<?php

namespace App\Containers\Users\Controllers;

use App\Containers\Common\Traits\PermissionControllersTrait;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\Response\ResponseHelper;
use App\Requests\PaginationRequest;

use App\Containers\Users\Requests\UpdateUserContactDataRequest;
use App\Containers\Users\Requests\DeleteUserContactDataRequest;
use App\Containers\Users\Validators\UsersValidators;
use App\Containers\Users\Requests\UserArraysRequest;
use App\Containers\Users\Requests\CreateUserRequest;
use App\Containers\Users\Requests\UpdateUserRequest;

use App\Containers\Users\Helpers\UserHelper;
use App\Containers\Users\Helpers\UserAgencyHelper;
use App\Containers\Common\Helpers\ContactHelper;
use App\Containers\Users\Helpers\UserRolesHelper;
use App\Containers\Agencies\Helpers\AgencyHelper;

use App\Exceptions\Common\NotAllowedException;
use Exception;


class UsersController extends Controller
{
    use ResponseHelper, UsersValidators, PermissionControllersTrait;

    /**
     * Checks if user exists by id
     * 
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function exists(string $id)
    {
        try {
            UserHelper::id($id);
            return $this->response('USERS.EXISTS');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.NOT_EXISTS', $e);
        }
        return $this->errorResponse('USERS.NOT_EXISTS');
    }

    /**
     * Checks if user exists by email
     * 
     * @param string $email
     * @return \Illuminate\Http\Response
     */
    public function existsEmail(string $email)
    {
        try {
            UserHelper::email($email);
            return $this->response('USERS.EXISTS');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.NOT_EXISTS', $e);
        }
        return $this->errorResponse('USERS.NOT_EXISTS');
    }

    /**
     * Get all users
     * 
     * @param PaginationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function get(PaginationRequest $request)
    {
        try {
            $this->allowedAction(['get-users'], 'USERS.GET_ALLOWED_ERROR');

            $data = UserHelper::getAll($request->get('pagination'));
            $info = [
                'meta' => $this->metaData($data),
                'users' => $data->data
            ];

            return $this->response('USERS.GET', $info);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.GET_ERROR', $e);
        }
        return $this->errorResponse('USERS.GET_ERROR');
    }

    /**
     * Get User By Id
     * 
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function id(string $id)
    {
        try {
            $this->allowedAction(['get-users'], 'USERS.GET_ALLOWED_ERROR');
            $user = UserHelper::full($id);
            
            $info = [
                'user' => $user,
            ];

            return $this->response('USERS.GET_ID', $info);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.GET_ERROR', $e);
        }

        return $this->errorResponse('USERS.GET_ERROR');
    }

    /**
     * Create a new user
     * 
     * @param CreateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CreateUserRequest $request)
    {
        try {
            $this->allowedAction(['create-users'], 'USERS.CREATE_USER_NOT_ALLOWED');
            $data = $request->all();
            $user = UserHelper::create($data);

            return $this->response('USERS.CREATE_USER_SUCCESS', ['user' => $user]);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.CREATE_USER_FAILED', $e);
        }

        return $this->errorResponse('USERS.CREATE_USER_FAILED');
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
        try {
            $this->allowedAction(['update-users'], 'USERS.UPDATE_USER_NOT_ALLOWED');
            $data = $request->all();
            $user = UserHelper::id($id);

            $this->crossAuthorization([$id]);

            $user = UserHelper::update($user, $data);

            return $this->response('USERS.UPDATE_USER_SUCCESS', ['user' => $user]);

        } catch (Exception $e) {
            return $this->errorResponse('USERS.UPDATE_USER_FAILED', $e);
        }
        return $this->errorResponse('USERS.UPDATE_USER_FAILED');
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
        try {
            $this->allowedAction(['update-users'], 'USERS.UPDATE_USER_NOT_ALLOWED');

            $data = $request->all();
            $user = UserHelper::id($id);

            $this->crossAuthorization([$id]);

            foreach($data['contact'] as $contactData) {
                $data = [
                    'type_id' => $contactData['type_id'],
                    'value' => trim($contactData['value']),
                ];

                isset($contactData['hidden']) ? $data['hidden'] = $contactData['hidden'] : $data['hidden'] = false;

                if(isset($contactData['id'])) {
                    // update the contact
                    $contact = ContactHelper::id($contactData['id']);
                    UserHelper::canSubmitContact($user, $data, $contact); // this will throw exception if submit is not allowed
                    ContactHelper::update($contact, $data, 'users', $user->id);
                } else {
                    UserHelper::canSubmitContact($user, $data); // this will throw exception if submit is not allowed
                    // create a new contact
                    ContactHelper::create($data, 'users', $user->id);
                }
            }

            $user = UserHelper::full($user->id);
            return $this->response('USERS.USER_CONTACT_DATA_UPDATED', ['user' => $user]);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.USER_CONTACT_DATA_UPDATE_FAILED', $e);
        }
        return $this->errorResponse('USERS.USER_CONTACT_DATA_UPDATE_FAILED');
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
        try {
            $this->allowedAction(['update-users'], 'USERS.UPDATE_USER_NOT_ALLOWED');
            $user = UserHelper::id($id);
            $this->crossAuthorization([$id]);

            $contactIds = $request->all()['contact'];

            foreach ($contactIds as $contactId) {
                if(!$user->contact()->where('id', $contactId)->count()) {
                    throw new NotAllowedException('', 'USERS.USER_CONTACT_ID_IS_DIFFERENT');
                }
                ContactHelper::delete($contactId);
            }

            return $this->response('USERS.USER_CONTACT_DATA_DELETED', ['user' => $user]);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.USER_CONTACT_DATA_DELETE_FAILED', $e);
        }
        return $this->errorResponse('USERS.USER_CONTACT_DATA_DELETE_FAILED');
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
        try {
            $this->allowedAction(['attach-permissions'], 'USERS.ATTACH_PERMISSIONS_NOT_ALLOWED');

            $data = $request->all();
            $this->permissions_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            foreach($data['permissions'] as $permissionId) {
                $this->allowedEditPermission($permissionId, 'USERS.ATTACH_PERMISSION_FAILED_LEVEL');
                UserHelper::attachPermission($user, $permissionId);
            }

            return $this->response('USERS.ATTACH_PERMISSIONS');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.ATTACH_PERMISSIONS_FAILED', $e);
        }
        return $this->errorResponse('USERS.ATTACH_PERMISSIONS_FAILED');
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
        try {
            $this->allowedAction(['attach-permissions'], 'USERS.ATTACH_PERMISSIONS_NOT_ALLOWED');

            $data = $request->all();
            $this->permissions_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            foreach($data['permissions'] as $permissionId) {
                $this->allowedEditPermission($permissionId, 'USERS.ATTACH_PERMISSION_FAILED_LEVEL');
                UserHelper::detachPermission($user, $permissionId);
            }

            return $this->response('USERS.DETACH_PERMISSIONS');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.DETACH_PERMISSIONS_FAILED', $e);
        }
        return $this->errorResponse('USERS.DETACH_PERMISSIONS_FAILED');
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
        try {
            $this->allowedAction(['attach-roles'], 'USERS.ATTACH_ROLES_NOT_ALLOWED');

            $data = $request->all();
            $this->roles_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            $currentUser = $request->user();
            $currentUserHighestRole = UserRolesHelper::getCurrentHighestRole();
            $isSuper = $currentUser->isSuper();

            foreach($data['roles'] as $roleId) {
                if(!$isSuper && $roleId <= $currentUserHighestRole->id) {
                    throw new NotAllowedException('', 'USERS.ATTACH_ROLES_FAILED_LEVEL');
                }
                UserHelper::attachRole($user, $roleId);
                UserRolesHelper::addRolePermissionsToUser($user, $roleId);
            }


            return $this->response('USERS.ATTACH_ROLES');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.ATTACH_ROLES_FAILED', $e);
        }
        return $this->errorResponse('USERS.ATTACH_ROLES_FAILED');
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
        try {
            $this->allowedAction(['attach-roles'], 'USERS.ATTACH_ROLES_NOT_ALLOWED');

            $data = $request->all();
            $this->roles_user($data)->validate();
            $user = UserHelper::id($data['user_id']);

            $this->crossAuthorization([$data['user_id']]);

            $currentUser = $request->user();
            $currentUserHighestRole = UserRolesHelper::getCurrentHighestRole();
            $isSuper = $currentUser->isSuper();

            foreach($data['roles'] as $roleId) {
                if(!$isSuper && $roleId <= $currentUserHighestRole->id) {
                    throw new NotAllowedException('', 'USERS.ATTACH_ROLES_FAILED_LEVEL');
                }
                UserHelper::detachRole($user, $roleId);
                UserRolesHelper::removeRolePermissionsToUser($user, $roleId);
            }

            return $this->response('USERS.DETACH_ROLES');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.DETACH_ROLES_FAILED', $e);
        }
        return $this->errorResponse('USERS.DETACH_ROLES_FAILED');
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
        try {
            $this->allowedAction(['activate-user'], 'USERS.ACTIVATE_DEACTIVATE_USER_NOT_ALLOWED');
            $ids = $request->get('user_ids');
            
            $this->crossAuthorization($ids);

            foreach($ids as $id) {
                UserHelper::inActivate(UserHelper::id($id));
            }

            return $this->response('USERS.DEACTIVATE');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.DEACTIVATE_ERROR', $e);
        }
        return $this->errorResponse('USERS.DEACTIVATE_ERROR');
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
        try {
            $this->allowedAction(['activate-user'], 'USERS.ACTIVATE_DEACTIVATE_USER_NOT_ALLOWED');
            $ids = $request->get('user_ids');
            
            $this->crossAuthorization($ids);

            foreach($ids as $id) {
                UserHelper::activate(UserHelper::id($id));
            }

            return $this->response('USERS.ACTIVATE');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.ACTIVATE_ERROR', $e);
        }
        return $this->errorResponse('USERS.ACTIVATE_ERROR');
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
        try {
            $this->allowedAction(['delete-user'], 'USERS.DELETE_USER_NOT_ALLOWED');
            $ids = $request->get('user_ids');
            
            $this->crossAuthorization($ids);

            foreach($ids as $id) {
                UserHelper::deleteUser(UserHelper::id($id));
            }

            return $this->response('USERS.DELETE_SUCCESSFUL');
        } catch (Exception $e) {
            return $this->errorResponse('USERS.DELETE_ERROR', $e);
        }
        return $this->errorResponse('USERS.DELETE_ERROR');
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
            $this->allowedAction(['get-deleted-users'], 'USERS.GET_ERROR');
            $data = UserHelper::getDeleted($request->get('pagination'));
            
            $info = [
                'meta' => $this->metaData($data),
                'users' => $data->data
            ];

            return $this->response('USERS.GET', $info);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.GET_ERROR', $e);
        }
        return $this->errorResponse('USERS.GET_ERROR');
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
            $this->allowedAction(['get-inactive-users'], 'USERS.GET_ERROR');
            $data = UserHelper::getInActivated($request->get('pagination'));
            
            $info = [
                'meta' => $this->metaData($data),
                'users' => $data->data
            ];

            return $this->response('USERS.GET', $info);
        } catch (Exception $e) {
            return $this->errorResponse('USERS.GET_ERROR', $e);
        }
        return $this->errorResponse('USERS.GET_ERROR');
    }

    /**
     * Set user as an admin to an agency
     * 
     * @param int $userId
     * @param int $agencyId
     * @return \Illuminate\Http\Response
     */
    public function setUserAsAgencyAdmin(int $userId, int $agencyId)
    {
        try {
            $this->allowedAction(['write-user-agency-admin'], 'USER_AGENCY_ADMIN_NOT_ALLOWED');
            $this->crossAuthorization([$userId]);

            $agency = AgencyHelper::id($agencyId);
            $user = UserHelper::id($userId);

            UserAgencyHelper::addUserAsAnAgencyAdmin($user, $agency);

            return $this->response('USER_AGENCY_ADMIN_SUCCESSFUL');
        } catch (Exception $e) {
            return $this->errorResponse('USER_AGENCY_ADMIN_FAIL', $e);
        }
        return $this->errorResponse('USER_AGENCY_ADMIN_FAIL');
    }

    /**
     * Remove user from being an admin to an agency
     * 
     * @param int $userId
     * @param int $agencyId
     * @return \Illuminate\Http\Response
     */
    public function removeUserAsAgencyAdmin(int $userId, int $agencyId)
    {
        try {
            $this->allowedAction(['write-user-agency-admin'], 'USER_AGENCY_ADMIN_NOT_ALLOWED');
            $this->crossAuthorization([$userId]);

            $agency = AgencyHelper::id($agencyId);
            $user = UserHelper::id($userId);

            UserAgencyHelper::revokeUserAsAnAgencyAdmin($user, $agency);

            return $this->response('USER_AGENCY_ADMIN_SUCCESSFUL');
        } catch (Exception $e) {
            return $this->errorResponse('USER_AGENCY_ADMIN_FAIL', $e);
        }
        return $this->errorResponse('USER_AGENCY_ADMIN_FAIL');
    }
}