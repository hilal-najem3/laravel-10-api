<?php

namespace App\Containers\Common\Helpers;

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
                throw new NotFoundException('CONTACT.CONTACT_TYPE_EXCEPTION');
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
                throw new NotFoundException('CONTACT.CONTACT_TYPE_EXCEPTION');
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
        DB::beginTransaction();
        try {
            $data = self::trim($data);
            if($data == null || $data['name'] == null) {
                throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
            }
            
            $type = ContactType::create($data);

            DB::commit();
            return self::id($type->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
        }

        throw new  CreateFailedException('CONTACT.CONTACT_EXCEPTION');
    }

    private static function trim(array $data)
    {
    }
}