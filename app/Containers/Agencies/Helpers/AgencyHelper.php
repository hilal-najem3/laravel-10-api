<?php

namespace App\Containers\Agencies\Helpers;

use Illuminate\Support\Facades\DB;

use App\Helpers\Storage\StoreHelper;
use App\Containers\Files\Models\Image;
use App\Containers\Files\Helpers\ImagesHelper;

use App\Containers\Agencies\Models\Agency;

use App\Containers\Agencies\Exceptions\AgencyDuplicateUserNameException;
use App\Exceptions\Common\ArgumentNullException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Helpers\BaseHelper;

class AgencyHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'AGENCY';
    protected static string $modelName = 'Agency';
    protected static string $model = Agency::class;

    protected static function model()
    {
        return self::$model;
    }

    protected static function message()
    {
        return self::$messageKeyBase;
    }

    /**
     * get agency type base info
     * by id
     * 
     * @param int $id
     * @return Agency $agency
     */
    public static function id(int $id)
    {
        try {
            $agency = Agency::find($id);

            if(!$agency) {
                throw new NotFoundException('AGENCY.NOT_FOUND');
            }

            return $agency;
        } catch (Exception $e) {
            throw new NotFoundException('AGENCY.NOT_FOUND');
        }

        throw new NotFoundException('AGENCY.NOT_FOUND');
    }

    /**
     * get agency type full info
     * by id
     * 
     * @param int $id
     * @return Agency $agency
     */
    public static function full(int $id)
    {
        try {
            $agency = Agency::with(['type', 'parent_agency', 'logo'])->where('id', $id)->first();

            if(!$agency) {
                throw new NotFoundException('AGENCY.NOT_FOUND');
            }

            if($agency->logo) {
                $agency->logo->link = StoreHelper::getFileLink($agency->logo->link);
            }

            return $agency;
        } catch (Exception $e) {
            throw new NotFoundException('AGENCY.NOT_FOUND');
        }

        throw new NotFoundException('AGENCY.NOT_FOUND');
    }

    /**
     * get all agency types base info
     * 
     * @return Agency[] $agencies
     */
    public static function all()
    {
        try {
            $agencies = Agency::with(['type', 'parent_agency', 'logo'])
            ->get()->each(function (Agency $agency) {
                if($agency->logo) {
                    $agency->logo->link = StoreHelper::getFileLink($agency->logo->link);
                }
            });

            if(!$agencies) {
                throw new NotFoundException('AGENCY.NOT_FOUND');
            }

            return $agencies;
        } catch (Exception $e) {
            throw new NotFoundException('AGENCY.NOT_FOUND');
        }

        throw new NotFoundException('AGENCY.NOT_FOUND');
    }

    /**
     * create a new Agency
     * 
     * @param  array $data
     * @return Agency | CreateFailedException
     */
    public static function create(array $data)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $data = self::trim($data);
            if(isset($data['agency_id'])) {
                $data['is_branch'] = true;
            }
            if($data['username']) {
                $agencyNameCount = Agency::where('username', $data['username'])->count();
                if($agencyNameCount) {
                    $ex = 'AgencyDuplicateUserNameException';
                    throw new AgencyDuplicateUserNameException();
                }
            }
            $agency = Agency::create($data);
            DB::commit();
            return self::id($agency->id);
        } catch (Exception $e) {
            DB::rollBack();
            if($ex != 'CreateFailedException' && $ex != 'AgencyDuplicateUserNameException') {
                throw new CreateFailedException('AGENCY.CREATE_FAILED');
            }
            throw $e;
        }
        throw new CreateFailedException('AGENCY.CREATE_FAILED');
    }

    /**
     * update an Agency
     * 
     * @param  Agency $agency
     * @param  array $data
     * @return Agency | UpdateFailedException | AgencyDuplicateUserNameException
     */
    public static function update(Agency $agency, array $data)
    {
        $ex = '';
        DB::beginTransaction();
        try {
            $data = self::trim($data);
            if($agency == null || !self::checkRequired($data)) {
                $ex = 'UpdateFailedException';
                throw new UpdateFailedException('AGENCY.UPDATE_FAILED');
            }

            if($data['username'] != $agency->username) {
                $agencyNameCount = Agency::where('username', $data['username'])->count();
                if($agencyNameCount) {
                    $ex = 'AgencyDuplicateUserNameException';
                    throw new AgencyDuplicateUserNameException();
                }
            }

            $agency->name = $data['name'];
            $agency->username = $data['username'];
            $agency->type_id = $data['type_id'];

            isset($data['bio']) ?
                $agency->bio = $data['bio'] : $agency->bio = '';

            if(isset($data['agency_id'])) {
                $agency->agency_id = $data['agency_id'];
                $agency->is_branch = true;
            } else {
                $agency->agency_id = null;
                $agency->is_branch = false;
            }

            isset($data['active']) ? $agency->active = $data['active'] : $agency->active = false;
            
            $agency->save();
            DB::commit();

            return self::id($agency->id);
        } catch (Exception $e) {
            DB::rollBack();
            if($ex != 'UpdateFailedException' && $ex != 'AgencyDuplicateUserNameException') {
                throw new UpdateFailedException('AGENCY.UPDATE_FAILED');
            }
            throw $e;
        }
        throw new UpdateFailedException('AGENCY.UPDATE_FAILED');
    }

    /**
     * This function updates the logo photo of the agency
     * 
     * @param Agency $agency
     * @param $photo
     * @param $photoSize
     * @return Image $image | UpdateFailedException
     */
    public static function updateLogo(Agency $agency, $photo, $photoSize = null)
    {
        DB::beginTransaction();
        try {
            $subPath = 'uploads/images/agencies/' . $agency->id;
            $image = $agency->logo()->first();

            if($photo != null) {
                $path = StoreHelper::storeFile($photo, $subPath);

                if($image) {
                    StoreHelper::deleteFile($image->link);
                    $data = [
                        'link' => $path,
                        'size' => $photoSize
                    ];
                    $image = ImagesHelper:: update($image, $data, 'logo');
                } else {
                    $image = ImagesHelper::create([
                        'link' => $path,
                        'size' => $photoSize
                    ], 'logo');
                }
    
                $agency->logo_id = $image->id;
            } else {
                if($image) {
                    StoreHelper::deleteFile($image->link);
                    $image->delete();
                }
                $agency->logo_id = null;
            }
            
            $agency->save();
            DB::commit();
            return $image;
        } catch (Exception $e) {
            DB::rollback();
            throw new UpdateFailedException('AGENCY.LOGO_ERROR');
        }

        throw new UpdateFailedException('AGENCY.LOGO_ERROR');
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
        if(isset($data['username'])) {
            $data['username'] = trim($data['username']);
        }
        if(isset($data['bio'])) {
            $data['bio'] = trim($data['bio']);
        }
        return $data;
    }

    /**
     * This function checks if the received data has an important
     * required piece of Agency data missing
     * returns true or false depending on the case
     * 
     * @param array $data
     * @return boolean
     */
    private static function checkRequired(array $data)
    {
        if(!isset($data['name']) || !isset($data['username']) || !isset($data['type_id']) ) {
            return false;
        }

        return true;
    }
}