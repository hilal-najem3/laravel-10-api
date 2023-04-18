<?php

namespace App\Containers\Common\Helpers;

use App\Containers\Users\Helpers\UserHelper;

use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Containers\Common\Models\Contact;

use Illuminate\Support\Facades\DB;

use App\Helpers\BaseHelper;

class ContactHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'CONTACT';
    protected static string $modelName = 'Contact';
    protected static string $model = Contact::class;
    protected static $allowed = ['id', 'all'];

    protected static function model()
    {
        return self::$model;
    }

    protected static function message()
    {
        return self::$messageKeyBase;
    }

    protected static function allowed()
    {
        return self::$allowed;
    }

    /**
     * get [contact] by id
     * 
     * @param int $id
     * @return Contact $contact
     */
    public static function id(int $id)
    {
        try {
            $contact = parent::id($id);
            $contact = $contact->load(['users', 'type']);
            return $contact;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * create a new contact
     * 
     * @param  array $data
     * @param string $targetTag
     * @param int $targetId
     * @return Contact | CreateFailedException
     */
    public static function create(array $data, string $targetTag, int $targetId)
    {
        DB::beginTransaction();
        try {
            // $data should contain the value and type id of the contact
            // where value is a string and type id is a integer existing in ContactType
            // and target tag should not be null it should connect the contact to user, agency, ...
            // and targetId is the id of the object that have that contact like the id if the actual user user
            if(
                $data == null ||
                $data['value'] == null ||
                $data['type_id'] == null ||
                $targetTag == null ||
                $targetId == null
                ) {
                throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
            }

            $data['value'] = trim($data['value']);
            
            $contact = Contact::create($data);

            switch(strtolower(trim($targetTag))) {
                case 'users': {
                    $user = UserHelper::id($targetId);
                    if($user == null) {
                        throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
                    }
                    $contact->users()->attach([$targetId]);
                    break;
                }
                default: {
                    throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
                    break;
                }
            }

            DB::commit();
            return self::id($contact->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
        }

        throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
    }

    /**
     * update contact
     * 
     * @param Contact $contact
     * @param  array $data
     * @param string $targetTag
     * @param int $targetId
     * @return Contact | UpdateFailedException
     */
    public static function update(Contact $contact, array $data, string $targetTag, int $targetId)
    {
        DB::beginTransaction();
        try {
            // $contact is the contact object that should be updated
            // $data should contain the value and type id of the contact
            // where value is a string and type id is a integer existing in ContactType
            // and target tag should not be null it should connect the contact to user, agency, ...
            // and targetId is the id of the object that have that contact like the id if the actual user user
            if(
                $data == null ||
                $data['value'] == null ||
                $data['type_id'] == null ||
                $targetTag == null ||
                $targetId == null
                ) {
                throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
            }

            $data['value'] = trim($data['value']);
            
            $contact->value = $data['value'];
            $contact->type_id = $data['type_id'];
            $contact->save();

            $contact->users()->detach();
            
            switch(strtolower(trim($targetTag))) {
                case 'users': {
                    $user = UserHelper::id($targetId);
                    if($user == null) {
                        throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
                    }
                    $contact->users()->attach([$targetId]);
                    break;
                }
                default: {
                    throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
                    break;
                }
            }

            DB::commit();
            return self::id($contact->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
        }

        throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
    }

    /**
     * delete contact
     * 
     * @param int $id
     * @return boolean | DeleteFailedException
     */
    public static function delete(int $id)
    {
        DB::beginTransaction();
        try {
            // This function doesn't detach child value like users
            // The detach happens on cron job delete
            // And that is due to soft deletion
            // So that we can still restore where this contact info is to
            $contact = Contact::find($id);

            if($contact == null) {
                throw new DeleteFailedException('CONTACT.CONTACT_EXCEPTION');
            }

            $contact->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new  DeleteFailedException('CONTACT.CONTACT_EXCEPTION');
        }

        throw new  DeleteFailedException('CONTACT.CONTACT_EXCEPTION');
    }

    /**
     * restore deleted contact
     * 
     * @param int $id
     * @return Contact | UpdateFailedException
     */
    public static function restore(int $id)
    {
        DB::beginTransaction();
        try {
            // This function restores deleted contact if it wasn't deleted by force deletion
            $contact = Contact::onlyTrashed()->where('id', $id)->first();

            if($contact == null) {
                throw new UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
            }

            $contact->restore();
            DB::commit();
            return self::id($contact->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
        }

        throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
    }
}