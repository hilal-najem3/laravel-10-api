<?php

namespace App\Containers\Files\Helpers;

use Illuminate\Support\Facades\DB;

use App\Containers\Files\Models\Image;
use App\Containers\Files\Models\ImageType;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\UpdateFailedException;
use App\Helpers\Storage\StoreHelper;
use Exception;

class ImagesHelper
{
    /**
     * get all image types
     * 
     * @return ImageType[] $types
     */
    public static function types()
    {
        try {
            $types = ImageType::all();

            return $types;
        } catch (Exception $e) {
            throw new NotFoundException('Image types');
        }
    }

    /**
     * get type id by type name
     * 
     * @param string $typeName
     * @return int $id
     */
    public static function getTypeIdByName(string $typeName)
    {
        try {
            $type = ImageType::where('name', $typeName)->first();

            if($type == null) {
                throw new NotFoundException('Image type');
            }

            return $type->id;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Get image by id
     * 
     * Load image type data if raw flag was false
     * else return image with find function only
     * 
     * @param int $id
     * @param bool $raw
     * @return Image
     */
    public static function getImageById(int $id, bool $raw = false): Image
    {
        try {
            $image = Image::find($id);

            if($image == null) {
                throw new NotFoundException('Image');
            }

            if(!$raw) {
                $image = $image->load(['type']);
            }

            return $image;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * This function create an image in the database using
     * its data, where type name can be sent as a string
     * 
     * @param array $data
     * @param string $typeName
     * @return Image $image
     */
    public static function create(array $data, string $typeName = null): Image
    {
        DB::beginTransaction();
        try {
            if($typeName != null) {
                $typeId = self::getTypeIdByName($typeName);
                $data['type_id'] = $typeId;
            }
            
            $image = Image::create($data);
            DB::commit();

            return self::getImageById($image->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * This function create an image in the database using
     * its data, where type name can be sent as a string
     * 
     * @param int $id
     * @param array $data
     * @param string $typeName
     * @return Image $image
     */
    public static function updateById(int $id, array $data, string $typeName = null): Image
    {
        DB::beginTransaction();
        try {
            if($typeName != null) {
                $typeId = self::getTypeIdByName($typeName);
                $data['type_id'] = $typeId;
            }
            
            $image = Image::find($id);

            if($image == null) {
                throw new UpdateFailedException('Image');
            }

            $image->link = $data['link'];
            $image->size = $data['size'];
            if(isset($data['type_id'])) {
                $image->type_id = $data['type_id'];
            } else {
                $image->type_id = null;
            }
            $image->save();
            DB::commit();

            return self::getImageById($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * This function create an image in the database using
     * its data, where type name can be sent as a string
     * 
     * @param Image $image
     * @param array $data
     * @param string $typeName
     * @return Image $image
     */
    public static function update(Image $image, array $data, string $typeName = null): Image
    {
        DB::beginTransaction();
        try {
            if($image == null) {
                throw new UpdateFailedException('Image');
            }

            if($typeName != null) {
                $typeId = self::getTypeIdByName($typeName);
                $data['type_id'] = $typeId;
            }

            $image->link = $data['link'];
            $image->size = $data['size'];
            if(isset($data['type_id'])) {
                $image->type_id = $data['type_id'];
            } else {
                $image->type_id = null;
            }
            $image->save();
            DB::commit();

            return self::getImageById($image->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * This function deletes an image from the database
     * It also deletes the associated file in storage if asked
     * 
     * @param int $id
     * @param bool $deleteFile
     * @return bool
     */
    public static function delete(int $id, bool $deleteFile = false): bool
    {
        DB::beginTransaction();
        try {
            $image = Image::find($id);

            if($image == null) {
                throw new DeleteFailedException('Image');
            }

            if($deleteFile) {
                StoreHelper::deleteFile($image->link);
            }
            
            $image->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}