<?php

namespace App\Containers\Common\Helpers;

use App\Containers\Common\Exceptions\ContactTypeDuplicateNameException;
use App\Containers\Common\Exceptions\ContactTypeDeleteFailedException;

use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Containers\Common\Models\ContactType;

use Illuminate\Support\Facades\DB;

class ContactTypesHelper
{
    /**
     * get all contact types
     * 
     * @return ContactType[] $regions
     */
    public static function all()
    {
        try {
            $types = ContactType::all();
            return $types;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * get contact type by id
     * 
     * @param int $id
     * @return ContactType $type
     */
    public static function id(int $id)
    {
        try {
            $type = ContactType::find($id);

            if($type == null) {
                throw new NotFoundException('CONTACT_TYPES.NAME');
            }
            
            return $type;
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
    public static function typeName(string $name)
    {
        try {
            $type = ContactType::where('name', trim($name))->first();

            if($type == null) {
                throw new NotFoundException('CONTACT_TYPES.NAME');
            }
            
            return $type;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * create a new contact type
     * 
     * @param  array $data
     * @return ContactType | CreateFailedException
     */
    public static function create(array $data)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $data = self::trim($data);
            if($data == null || $data['name'] == null) {
                throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
            }
            if(ContactType::where('name', $data['name'])->get()->count()) {
                $ex = 'ContactTypeDuplicateNameException';
                throw new ContactTypeDuplicateNameException();
            }
            
            $type = ContactType::create($data);

            DB::commit();
            return self::id($type->id);
        } catch (Exception $e) {
            DB::rollBack();
            $ex == 'ContactTypeDuplicateNameException' ? 
            throw new ContactTypeDuplicateNameException() :
            throw new  CreateFailedException('CONTACT_TYPES.NAME');
        }

        throw new  CreateFailedException('CONTACT_TYPES.NAME');
    }

    /**
     * update a contact type
     * 
     * @param ContactType $contactType
     * @param  array $data
     * @return ContactType | UpdateFailedException
     */
    public static function update(ContactType $contactType, array $data)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $data = self::trim($data);
            if($data == null || $data['name'] == null) {
                throw new  UpdateFailedException('CONTACT.CONTACT_EXCEPTION');
            }
            if($contactType->name != $data['name'] &&ContactType::where('name', $data['name'])->get()->count()) {
                $ex = 'ContactTypeDuplicateNameException';
                throw new ContactTypeDuplicateNameException();
            }
            
            $contactType->name = $data['name'];
            isset($data['allow_duplicates']) ? 
            $contactType->allow_duplicates = $data['allow_duplicates'] : 
            $contactType->allow_duplicates = false;

            isset($data['regex']) ? 
            $contactType->regex = $data['regex'] : 
            $contactType->regex = '';

            $contactType->save();

            DB::commit();
            return self::id($contactType->id);
        } catch (Exception $e) {
            DB::rollBack();
            $ex == 'ContactTypeDuplicateNameException' ? 
            throw new ContactTypeDuplicateNameException() :
            throw new  UpdateFailedException('CONTACT_TYPES.NAME');
        }

        throw new  UpdateFailedException('CONTACT_TYPES.NAME');
    }

    /**
     * delete a contact type
     * 
     * @param  ContactType $contactType
     * @return true | DeleteFailedException
     */
    public static function delete(ContactType $contactType)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            if($contactType->contacts()->count() > 0) {
                $ex = 'ContactTypeDeleteFailedException';
                throw new ContactTypeDeleteFailedException();
            }
            $contactType->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            $ex == 'ContactTypeDeleteFailedException' ? 
            throw new ContactTypeDeleteFailedException() :
            throw new  DeleteFailedException('CONTACT_TYPES.NAME');
        }

        throw new  DeleteFailedException('CONTACT_TYPES.NAME');
    }

    /**
     * Trims the appropriate data for contact type
     * 
     * @param array $data
     * @return array $data
     */
    private static function trim(array $data): array
    {
        isset($data['name']) ? $data['name'] = trim($data['name']): null;
        isset($data['regex']) ? $data['regex'] = trim($data['regex']): null;
        return $data;
    }
}