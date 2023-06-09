<?php

namespace App\Containers\Users\Helpers;


use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Helpers\ConstantsHelper;

use App\Helpers\Response\CollectionsHelper;
use App\Helpers\Storage\StoreHelper;

use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\NotAllowedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use Exception;

use App\Containers\Users\Exceptions\UpdatePasswordFailedException;
use App\Containers\Users\Exceptions\SameOldPasswordException;
use App\Containers\Users\Exceptions\DuplicateEmailException;
use App\Containers\Users\Exceptions\OldPasswordException;

use App\Containers\Files\Helpers\ImagesHelper;
use App\Containers\Auth\Helpers\UserTokenHelper;
use App\Containers\Common\Helpers\ContactHelper;
use App\Containers\Common\Helpers\ContactTypesHelper;

use App\Containers\Permissions\Models\Permission;
use App\Containers\Files\Models\Image;
use App\Containers\Common\Models\ContactUser;
use App\Containers\Common\Models\Contact;
use App\Containers\Roles\Models\Role;
use App\Models\User;

use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class UserHelper
{
    /**
     * get user base info (only from users table)
     * by id
     * 
     * @param int $id
     * @return User $user
     */
    public static function id(int $id)
    {
        try {
            $user = User::find($id);

            if(!$user) {
                throw new NotFoundException('PROFILE.EXCEPTION');
            }

            return $user;
        } catch (Exception $e) {
            Log::error('User not found - UserHelper::id(' . $id . ')');
            throw new NotFoundException('PROFILE.EXCEPTION');
        }

        throw new NotFoundException('PROFILE.EXCEPTION');
    }

    /**
     * get user base info (only from users table)
     * 
     * @param string $email
     * @return User $user
     */
    public static function email(string $email)
    {
        try {
            $user = User::where('email', $email)->first();

            if(!$user) {
                throw new NotFoundException('PROFILE.EXCEPTION');
            }

            return $user;
        } catch (Exception $e) {
            Log::error('User not found - UserHelper::email(' . $email . ')');
            throw new NotFoundException('PROFILE.EXCEPTION');
        }

        throw new NotFoundException('PROFILE.EXCEPTION');
    }

    /**
     * This function returns the current authenticated user profile information
     * 
     * @return User $user
     */
    public static function profile()
    {
        try {
            $user = Auth::user();

            if(!$user || $user == null) {
                throw new NotFoundException('USERS.USER');
            }

            $user = $user->load(['roles', 'profileImage', 'contact']);

            if(isset($user->profileImage)) {
                $user->profileImage->link = StoreHelper::getFileLink($user->profileImage->link);
            }
    
            Log::info('User profile returned');
    
            return $user;
        } catch (Exception $e) {
            Log::error('Get user profile failed - UserHelper::profile()');
            throw $e;
        }
    }

    /**
     * get user full info by id
     * 
     * @param int $id
     * @return User $user
     */
    public static function full(int $id)
    {
        try {
            $user = User::with(['roles', 'permissions', 'profileImage', 'contact'])->where('id', $id)->first();

            if(!$user) {
                throw new NotFoundException('PROFILE.EXCEPTION');
            }

            if($user->profileImage) {
                $user->profileImage->link = StoreHelper::getFileLink($user->profileImage->link);
            }

            return $user;
        } catch (Exception $e) {
            Log::error('User not found - UserHelper::id(' . $id . ')');
            throw new NotFoundException('PROFILE.EXCEPTION');
        }

        throw new NotFoundException('PROFILE.EXCEPTION');
    }

    /**
     * This function set the active filed for a user profile to false
     * which will prevent him from exercising login an all his activities
     * on this api
     * 
     * @param User $user
     * @return boolean | UpdateFailedException
     */
    public static function inActivate(User $user)
    {
        DB::beginTransaction();
        try {
            $user->active = false;
            $user->save();

            // revoke all this user's tokens
            UserTokenHelper::revoke_all($user);

            Log::info('User profile inactivated');
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UpdateFailedException('PROFILE.EXCEPTION');
        }

        throw new UpdateFailedException('PROFILE.EXCEPTION');
    }

     /**
     * This function set the active filed for a user profile to true
     * which will allow him to exercise login an all his allowed activities
     * on this api
     * 
     * @param User $user
     * @return boolean | UpdateFailedException
     */
    public static function activate(User $user)
    {
        DB::beginTransaction();
        try {
            $user->active = true;
            $user->save();

            Log::info('User profile is now activated');
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new UpdateFailedException('PROFILE.EXCEPTION');
        }

        throw new UpdateFailedException('PROFILE.EXCEPTION');
    }

    /**
     * get all users
     * 
     * @param int $paginationCount
     * @return pagination of users
     */
    public static function getAll(int $paginationCount = null)
    {
        try {
            $paginationCount = ConstantsHelper::getPagination($paginationCount);

            $users = User::with(['roles', 'permissions', 'profileImage'])
            ->get()->each(function (User $user) {
                if($user->profileImage) {
                    $user->profileImage->link = StoreHelper::getFileLink($user->profileImage->link);
                }
            });

            $users = CollectionsHelper::paginate($users, $paginationCount);
            
            $users = json_decode(json_encode($users)); // This will change its type to StdClass

            Log::info('User returned successfully');

            return $users;
        } catch (Exception $e) {
            Log::error('Get users failed - UserHelper::getAll()');
            throw $e;
        }

        return [];
    }

    /**
     * get deleted users
     * that were deleted by soft delete
     * 
     * @param int $paginationCount
     * @return pagination of users
     */
    public static function getDeleted(int $paginationCount = null)
    {
        try {
            $paginationCount = ConstantsHelper::getPagination($paginationCount);

            $users = User::onlyTrashed()->with(['roles', 'permissions', 'profileImage'])
            ->get()->each(function (User $user) {
                if($user->profileImage) {
                    $user->profileImage->link = StoreHelper::getFileLink($user->profileImage->link);
                }
            });

            $users = CollectionsHelper::paginate($users, $paginationCount);
            
            $users = json_decode(json_encode($users)); // This will change its type to StdClass

            Log::info('User returned successfully');

            return $users;
        } catch (Exception $e) {
            Log::error('Get users failed - UserHelper::getDeleted()');
            throw $e;
        }

        return [];
    }

    /**
     * get inactivated users
     * 
     * @param int $paginationCount
     * @return pagination of users
     */
    public static function getInActivated(int $paginationCount = null)
    {
        try {
            $paginationCount = ConstantsHelper::getPagination($paginationCount);

            $users = User::where('active', false)->with([
                'roles',
                'permissions',
                'profileImage',
                'contact'
                ])
            ->get()->each(function (User $user) {
                if($user->profileImage) {
                    $user->profileImage->link = StoreHelper::getFileLink($user->profileImage->link);
                }
            });

            $users = CollectionsHelper::paginate($users, $paginationCount);
            
            $users = json_decode(json_encode($users)); // This will change its type to StdClass

            Log::info('User returned successfully');

            return $users;
        } catch (Exception $e) {
            Log::error('Get users failed - UserHelper::getDeleted()');
            throw $e;
        }

        return [];
    }

    /**
     * create user
     * 
     * @param  array $data
     * @return User | CreateFailedException | Exception
     */
    public static function create(array $data)
    {
        DB::beginTransaction();
        try {
            $data = UserHelper::trimUserData($data);
            $data['password'] = Hash::make($data['password']);
            if(isset($data['dob']) && $data['dob'] != '') {
                $data['dob'] = new Carbon($data['dob']);
            }

            $user = User::create($data);
            DB::commit();

            Log::info('User created successfully');
            return self::id($user->id);
        } catch (Exception $e) {
            Log::error('User create failed on UserHelper::create() with data: ' . json_encode($data));
            DB::rollback();
            throw new CreateFailedException('PROFILE.EXCEPTION');
        }

        DB::rollback();
        throw new CreateFailedException('PROFILE.EXCEPTION');
    }
    
    /**
     * update user
     * 
     * @param  User $user
     * @param  array $data
     * @return User | DuplicateEmailException | UpdateFailedException
     */
    public static function update(User $user, array $data)
    {
        DB::beginTransaction();
        $activeException = '';
        try {
            $data = UserHelper::trimUserData($data);
            if(isset($data['dob']) && $data['dob'] != '') {
                $data['dob'] = new Carbon($data['dob']);
            }
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];

            if($user->email != $data['email']) {
                $emailCount = User::where('email',  $data['email'])->count();
                if($emailCount) {
                    $activeException = 'DuplicateEmailException';
                    // New Email exists
                    throw new DuplicateEmailException();
                }

                $user->email = $data['email'];
            }

            $user->save();
            DB::commit();
            Log::info('User data updated successfully');
            $user = self::id($user->id);
            return $user;
        } catch (Exception $e) {
            Log::error('User data updated failed - UserHelper::update()');
            DB::rollback();
            switch($activeException) {
                case 'DuplicateEmailException': {
                    throw new DuplicateEmailException();
                    break;
                }
                default: {
                    throw new UpdateFailedException('PROFILE.EXCEPTION');
                    break;
                }
            }
        }

        DB::rollback();
        throw new UpdateFailedException('PROFILE.EXCEPTION');
    }

    /**
     * update user password
     * 
     * @param  User $user
     * @param  array $data
     * @return User | OldPasswordException | SameOldPasswordException | UpdatePasswordFailedException
     */
    public static function updatePassword(User $user, array $data)
    {
        DB::beginTransaction();
        $activeException = '';

        try {
            $data = UserHelper::trimPasswords($data);

            if(!Hash::check($data['old_password'], $user->password)) {
                $activeException = 'OldPasswordException';
                throw new OldPasswordException();
            }

            if(Hash::check($data['password'], $user->password)) {
                $activeException = 'SameOldPasswordException';
                throw new SameOldPasswordException();
            }

            $user->password = Hash::make($data['password']);
            $user->password_updated_at = now();
            $user->save();

            DB::commit();

            return true;
        } catch (Exception $e) {
            Log::error('Update password failed - UserHelper::updatePassword');
            DB::rollback();
            switch($activeException) {
                case 'OldPasswordException': {
                    throw new OldPasswordException();
                    break;
                }
                case 'SameOldPasswordException': {
                    throw new SameOldPasswordException();
                    break;
                }
                default: {
                    throw new UpdatePasswordFailedException();
                    break;
                }
            }
        }

        throw new UpdatePasswordFailedException();
    }

    /**
     * This function updates the profile photo of the user
     * 
     * @param User $user
     * @param $photo
     * @param $photoSize
     * @return Image $image | UpdateFailedException
     */
    public static function updateProfilePhoto(User $user, $photo, $photoSize = null)
    {
        DB::beginTransaction();
        try {
            $subPath = 'uploads/images/users/' . $user->id;

            $image = $user->profileImage()->first();

            if($photo != null) {
                $path = StoreHelper::storeFile($photo, $subPath);

                if($image) {
                    StoreHelper::deleteFile($image->link);
                    $data = [
                        'link' => $path,
                        'size' => $photoSize
                    ];
                    $image = ImagesHelper:: update($image, $data, 'profile');
                } else {
                    $image = ImagesHelper::create([
                        'link' => $path,
                        'size' => $photoSize
                    ], 'profile');
                }
    
                $user->profile_image = $image->id;
            } else {
                if($image) {
                    StoreHelper::deleteFile($image->link);
                    $image->delete();
                }
                $user->profile_image = null;
            }
            
            $user->save();

            DB::commit();
            Log::info('User profile photo uploaded successfully');
            return $image;
        } catch (Exception $e) {
            Log::error('User profile photo upload failed - UserHelper::updateProfilePhoto()');
            DB::rollback();
            if($e->getMessage() != null) {
                // We have a normal exception
                throw new UpdateFailedException('PROFILE.EXCEPTION');
            }
            throw $e;
        }

        throw new UpdateFailedException('PROFILE.EXCEPTION');
    }

    /**
     * This function checks if this user's contact data is allowed to be created or updated.
     * that is it checks for the existence of duplicates
     * and if the contact id supplied is for the same user
     * 
     * @param User $user
     * @param array $contactData this should have id, type_id and value
     * @param Contact $contactModel
     * @return boolean | NotAllowedException
     */
    public static function canSubmitContact(User $user, array $contactData, Contact $contactModel = null)
    {
        $contactType = ContactTypesHelper::id($contactData['type_id']);
        $count = Contact::where('value', trim($contactData['value']))->count();
        $userDataCountValue = $user->contact()->where('value', trim($contactData['value']))->count(); // This the count of the contact data of this user having the same value that is submitted
        if($userDataCountValue) {
            throw new NotAllowedException('', 'USERS.USER_CONTACT_VALUE_SELF_DUPLICATE');
        }
        if($contactModel != null && $contactModel->id != null) {
            $exists = $user->contact()->where('id', $contactModel->id)->count();
            if(!$exists) {
                throw new NotAllowedException('', 'USERS.USER_CONTACT_ID_IS_DIFFERENT');
            }
            if(!$contactType->allow_duplicates && $contactData['value'] != $contactModel->value && $count) {
                throw new NotAllowedException('', 'USERS.USER_CONTACT_VALUE_IS_USED');
            }
        } else {
            if(!$contactType->allow_duplicates && $count) {
                throw new NotAllowedException('', 'USERS.USER_CONTACT_VALUE_IS_USED');
            }
        }

        return true;
    }

    /**
     * update user by attach permission
     * 
     * @param  User $user
     * @param  int $permissionId
     * @return boolean
     */
    public static function attachPermission(User $user, int $permissionId)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::find($permissionId);

            if(!$permission) {
                throw new NotFoundException('PERMISSION');
            }

            $user->permissions()->attach($permission);

            DB::commit();

            Log::info('Permission attached successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Permission attach failed - UserHelper::attachPermission');
            DB::rollback();
            return false;
        }

        DB::rollback();
        return false;
    }

    /**
     * update user by detach permission
     * 
     * @param  User $user
     * @param  int $permissionId
     * @return boolean
     */
    public static function detachPermission(User $user, int $permissionId)
    {
        DB::beginTransaction();

        try {
            $permission = Permission::find($permissionId);

            if(!$permission) {
                throw new NotFoundException('PERMISSION');
            }

            $user->permissions()->detach($permission);

            DB::commit();
            Log::info('Permission detached successfully');
            return true;
        } catch (\Exception $e) {
            Log::error('Permission detach failed - UserHelper::detachPermission');
            DB::rollback();
            return false;
        }

        DB::rollback();
        return false;
    }

    /**
     * update user by attach role
     * 
     * @param  User $user
     * @param  int $roleId
     * @return boolean
     */
    public static function attachRole(User $user, int $roleId)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($roleId);

            if(!$role) {
                throw new NotFoundException('ROLE');
            }

            $user->roles()->attach($role);

            DB::commit();
            Log::info('Role attached successfully');
            return true;
        } catch (Exception $e) {
            Log::info('Role attach failed - UserHelper::attachRole');
            DB::rollback();
            return false;
        }

        DB::rollback();
        return false;
    }

    /**
     * update user by detach role
     * 
     * @param  User $user
     * @param  int $roleId
     * @return boolean
     */
    public static function detachRole(User $user, int $roleId)
    {
        DB::beginTransaction();

        try {
            $role = Role::find($roleId);

            if(!$role) {
                throw new NotFoundException('ROLE');
            }

            $user->roles()->detach($role);

            DB::commit();
            Log::info('Role detached successfully');
            return true;
        } catch (Exception $e) {
            Log::info('Role detach failed - UserHelper::detachRole');
            DB::rollback();
            return false;
        }

        DB::rollback();
        return false;
    }

    /**
     * Delete user from database
     * and all his related data if light delete is false
     * Deactivate and soft delete user if light delete is true
     * 
     * @param User $user
     * @param bool $lightDelete
     * @return boolean | DeleteFailedException
     */
    public static function deleteUser(User $user, bool $lightDelete = false)
    {
        DB::beginTransaction();
        try {
            if(!$lightDelete) {
                // This user is deleted by a high admin
                // So we are fully deleting all data related to him
                $user->roles()->detach();
                $user->permissions()->detach();

                $contactData = $user->contact()->get();
                foreach($contactData as $contact) {
                    // We are deleting the contact this way to make sure that
                    // we can still restore it later and attached to this user
                    $contact->delete();
                }
    
                if($user->profile_image) {
                    $image = $user->profileImage()->first();
                    if($image) {
                        StoreHelper::deleteFile($image->link);
                        $image->delete();
                    }
                }

                $user->can_restore = false;
            }
            
            // revoke all this user's tokens
            UserTokenHelper::revoke_all($user);

            $user->active = false;
            $user->save();

            $user->delete();

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw new DeleteFailedException('USERS.USER');
        }
        throw new DeleteFailedException('USERS.USER');
    }

    /**
     * Delete user from database
     * and all his related data if light delete is false
     * Deactivate and soft delete user if light delete is true
     * 
     * @param User $user
     * @return boolean | UpdateFailedException
     */
    public static function restoreUser(User $user)
    {
        DB::beginTransaction();
        try {
            $user->restore();

            $user = self::id($user->id);
            $user->active = true;
            $user->save();

            $contactsUser = ContactUser::where('user_id', $user->id)->get();
            foreach ($contactsUser as $contactUser) {
                ContactHelper::restore($contactUser->contact_id);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            throw new UpdateFailedException('USERS.USER');
        }
        throw new UpdateFailedException('USERS.USER');
    }

    public static function trimUserData(array $data)
    {
        if(isset($data['first_name']) && $data['first_name'] != '') {
            $data['first_name'] = trim($data['first_name']);
        }
        if(isset($data['last_name']) && $data['last_name'] != '') {
            $data['last_name'] = trim($data['last_name']);
        }
        if(isset($data['email']) && $data['email'] != '') {
            $data['email'] = trim($data['email']);
        }
        if(isset($data['password']) && $data['password'] != '') {
            $data['password'] = trim($data['password']);
        }
        return $data;
    }

    public static function trimPasswords(array $data)
    {
        if(isset($data['old_password']) && $data['old_password'] != '') {
            $data['old_password'] = trim($data['old_password']);
        }
        if(isset($data['password']) && $data['password'] != '') {
            $data['password'] = trim($data['password']);
        }
        return $data;
    }
}