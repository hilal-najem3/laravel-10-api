<?php

namespace App\Containers\Agencies\Helpers;

use Illuminate\Support\Facades\DB;

use App\Containers\Agencies\Models\AgencyType;

use App\Containers\Agencies\Exceptions\AgencyTypeDuplicateNameException;
use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

class AgencyTypesHelper
{
    /**
     * get agency type base info
     * by id
     * 
     * @param int $id
     * @return AgencyType $agencyType
     */
    public static function id(int $id)
    {
        try {
            $agencyType = AgencyType::find($id);

            if(!$agencyType) {
                throw new NotFoundException('AGENCY_TYPES.NOT_FOUND');
            }

            return $agencyType;
        } catch (Exception $e) {
            throw new NotFoundException('AGENCY_TYPES.NOT_FOUND');
        }

        throw new NotFoundException('AGENCY_TYPES.NOT_FOUND');
    }

    /**
     * get all agency types base info
     * 
     * @return AgencyType[] $agencyTypes
     */
    public static function all()
    {
        try {
            $agencyTypes = AgencyType::all();

            if(!$agencyTypes) {
                throw new NotFoundException('AGENCY_TYPES.NOT_FOUND');
            }

            return $agencyTypes;
        } catch (Exception $e) {
            throw new NotFoundException('AGENCY_TYPES.NOT_FOUND');
        }

        throw new NotFoundException('AGENCY_TYPES.NOT_FOUND');
    }

    /**
     * create a new AgencyType
     * 
     * @param  array $data
     * @return AgencyType | CreateFailedException | AgencyTypeDuplicateNameException
     */
    public static function create(array $data)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            if(!isset($data['name'])) {
                $ex = 'CreateFailedException';
                throw new CreateFailedException('AGENCY_TYPES.CREATE_FAILED');
            }
            $data = self::trim($data);
            $agencyTypeNameCount = AgencyType::where('name', $data['name'])->count();
            if($agencyTypeNameCount) {
                $ex = 'AgencyTypeDuplicateNameException';
                throw new AgencyTypeDuplicateNameException();
            }
            $agencyType = AgencyType::create($data);
            DB::commit();
            return self::id($agencyType->id);
        } catch (Exception $e) {
            DB::rollBack();
            if($ex != 'CreateFailedException' && $ex != 'AgencyTypeDuplicateNameException') {
                throw new CreateFailedException('AGENCY_TYPES.CREATE_FAILED');
            }
            throw $e;
        }
        throw new CreateFailedException('AGENCY_TYPES.CREATE_FAILED');
    }

    /**
     * update an AgencyType
     * 
     * @param  AgencyType $type
     * @param  array $data
     * @return AgencyType | UpdateFailedException | AgencyTypeDuplicateNameException
     */
    public static function update(AgencyType $type, array $data)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $data = self::trim($data);
            if($type == null || !isset($data['name'])) {
                $ex = 'UpdateFailedException';
                throw new UpdateFailedException('AGENCY_TYPES.UPDATE_FAILED');
            }

            if($data['name'] != $type) {
                $agencyTypeNameCount = AgencyType::where('name', $data['name'])->count();
                if($agencyTypeNameCount) {
                    $ex = 'AgencyTypeDuplicateNameException';
                    throw new AgencyTypeDuplicateNameException();
                }
            }

            $type->name = $data['name'];
            isset($data['description']) ?
                $type->description = $data['description'] : $type->description = '';
            
            $type->save();
            DB::commit();

            return self::id($type->id);
        } catch (Exception $e) {
            DB::rollBack();
            if($ex != 'CreateFailedException' && $ex != 'AgencyTypeDuplicateNameException') {
                throw new UpdateFailedException('AGENCY_TYPES.UPDATE_FAILED');
            }
            throw $e;
        }
        throw new UpdateFailedException('AGENCY_TYPES.UPDATE_FAILED');
    }

    /**
     * This function trims the strings of array holding
     * agency type data from
     * 
     * @param array $data
     * @return array $data
     */
    private static function trim(array $data)
    {
        if(isset($data['name'])) {
            $data['name'] = trim($data['name']);
        }
        if(isset($data['description'])) {
            $data['description'] = trim($data['description']);
        }
        return $data;
    }
}