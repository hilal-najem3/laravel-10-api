<?php

namespace App\Containers\Common\Helpers;

use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Containers\Common\Messages\Messages;

use App\Containers\Common\Models\ContactType;
use App\Containers\Common\Models\Contact;

use Illuminate\Support\Facades\DB;

class ContactHelper
{
    use Messages;

    public static function getMessages()
    {
        $dataHelper = new DataHelper();
        $messages = $dataHelper->messages();
        return $messages;
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
     * create a new data object
     * 
     * @param  array $data
     * @param string $targetTag
     * @param int $id
     * @return Contact | CreateFailedException
     */
    public static function createContact(array $data, string $targetTag, int $id)
    {
        $messages = self::getMessages();
        DB::beginTransaction();
        try {
            // $data should contain the value and type id of the contact
            // where value is a string and type id is a integer existing in ContactType
            // and target tag should not be null it should connect the contact to user, agency, ...
            // and id is the id of the object that have that contact like the id if the actual user user
            if(
                $data == null ||
                $data['value'] == null ||
                $data['type_id'] == null ||
                $targetTag == null ||
                $id == null
                ) {
                throw new  CreateFailedException($messages['CONTACT']['CONTACT_EXCEPTION']);
            }
            
            $contact = Contact::create($data);

            switch(strtolower($targetTag)) {
                case 'users': {
                    $contact->users()->attach([$id]);
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
}