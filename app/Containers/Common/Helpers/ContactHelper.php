<?php

namespace App\Containers\Common\Helpers;

use App\Containers\Users\Helpers\UserHelper;

use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Containers\Common\Helpers\MessagesHelper;

use App\Containers\Common\Models\ContactType;
use App\Containers\Common\Models\Contact;

use Illuminate\Support\Facades\DB;

class ContactHelper
{
    public static function getMessages()
    {
        return MessagesHelper::messages();
    }

    /**
     * get all contact types
     * 
     * @return ContactType[] $regions
     */
    public static function types()
    {
        try {
            $types = ContactType::all();
            return $types;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get contact type by name
     * 
     * @param string $name
     * @return ContactType $type
     */
    public static function type(string $name)
    {
        $messages = self::getMessages();
        try {
            $type = ContactType::where('name', trim($name))->first();

            if($type == null) {
                throw new NotFoundException($messages['CONTACT']['CONTACT_TYPE_EXCEPTION']);
            }
            
            return $type;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get [contact] by id
     * 
     * @param int $id
     * @return Contact $contact
     */
    public static function id(int $id)
    {
        $messages = self::getMessages();
        $contact = Contact::find($id);

        if($contact == null) {
            throw new NotFoundException($messages['CONTACT']['CONTACT_EXCEPTION']);
        }

        $contact = $contact->load(['users', 'type']);

        return $contact;
    }

    /**
     * create a new contact
     * 
     * @param  array $data
     * @param string $targetTag
     * @param int $targetId
     * @return Contact | CreateFailedException
     */
    public static function createContact(array $data, string $targetTag, int $targetId)
    {
        $messages = self::getMessages();
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
                throw new  CreateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
            }
            
            $contact = Contact::create($data);

            switch(strtolower(trim($targetTag))) {
                case 'users': {
                    $user = UserHelper::id($targetId);
                    if($user == null) {
                        throw new  CreateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
                    }
                    $contact->users()->attach([$targetId]);
                    break;
                }
                default: {
                    throw new  CreateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
                    break;
                }
            }

            DB::commit();
            return self::id($contact->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  CreateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
        }

        throw new  CreateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
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
    public static function updateContact(Contact $contact, array $data, string $targetTag, int $targetId)
    {
        $messages = self::getMessages();
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
                throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
            }
            
            $contact->value = $data['value'];
            $contact->type_id = $data['type_id'];
            $contact->save();

            $contact->users()->detach();
            
            switch(strtolower(trim($targetTag))) {
                case 'users': {
                    $user = UserHelper::id($targetId);
                    if($user == null) {
                        throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
                    }
                    $contact->users()->attach([$targetId]);
                    break;
                }
                default: {
                    throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
                    break;
                }
            }

            DB::commit();
            return self::id($contact->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
        }

        throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
    }

    /**
     * delete contact
     * 
     * @param int $id
     * @return boolean | DeleteFailedException
     */
    public static function deleteContact(int $id)
    {
        $messages = self::getMessages();
        DB::beginTransaction();
        try {
            // This function doesn't detach child value like users
            // The detach happens on cron job delete
            // And that is due to soft deletion
            // So that we can still restore where this contact info is to
            $contact = Contact::find($id);

            if($contact == null) {
                throw new DeleteFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
            }

            $contact->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new  DeleteFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
        }

        throw new  DeleteFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
    }

    /**
     * restore deleted contact
     * 
     * @param int $id
     * @return Contact | UpdateFailedException
     */
    public static function restoreContact(int $id)
    {
        $messages = self::getMessages();
        DB::beginTransaction();
        try {
            // This function restores deleted contact if it wasn't deleted by force deletion
            $contact = Contact::onlyTrashed()->where('id', $id)->first();

            if($contact == null) {
                throw new UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
            }

            $contact->restore();
            DB::commit();
            return self::id($contact->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
        }

        throw new  UpdateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
    }
}