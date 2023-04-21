<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

use App\Exceptions\Common\NotFoundException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\UpdateFailedException;
use Exception;

use Illuminate\Database\Eloquent\Model;

abstract class BaseHelper
{
    abstract protected static function model();
    abstract protected static function message();
    abstract protected static function allowed();

    /**
     * get model's base info
     * 
     * @param int $id
     * @return Model
     */
    public static function id(int $id)
    {
        if(!self::checkAllowed('id')) {
            throw new NotFoundException(static::message() . '.NOT_FOUND');
        }
        try {
            $model = static::model()::find($id);

            if(!$model) {
                throw new NotFoundException(static::message() . '.NOT_FOUND');
            }

            return $model;
        } catch (Exception $e) {
            throw $e;
        }

        throw new NotFoundException(static::message() . '.NOT_FOUND');
    }

    /**
     * get all models base info
     * 
     * @return Model[]
     */
    public static function all()
    {
        if(!self::checkAllowed('all')) {
            throw new NotFoundException(static::message() . '.NOT_FOUND');
        }
        try {
            $models = static::model()::all();

            if(!$models) {
                throw new NotFoundException(static::message() . '.NOT_FOUND');
            }

            return $models;
        } catch (Exception $e) {
            throw new NotFoundException(static::message() . '.NOT_FOUND');
        }

        throw new NotFoundException(static::message() . '.NOT_FOUND');
    }

    /**
     * Create an instance of a model from an array of data
     * 
     * @param array $data
     * @return Model $model
     */
    public static function baseCreate(array $data): Model
    {
        if(!self::checkAllowed('create')) {
            throw new CreateFailedException(static::message() . '.NAME');
        }
        DB::beginTransaction();
        try {
            $model = static::model()::create($data);
            DB::commit();

            return self::id($model->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new CreateFailedException(static::message() . '.NAME');
        }
        throw new CreateFailedException(static::message() . '.NAME');
    }

    /**
     * Update an instance of a model using an array of data
     * 
     * @param Model $model
     * @param array $data
     * @return Model $model
     */
    public static function baseUpdate(Model $model, array $data): Model
    {
        if(!self::checkAllowed('update')) {
            throw new UpdateFailedException(static::message() . '.NAME');
        }
        DB::beginTransaction();
        try {
            $model = static::model()::update($data);
            DB::commit();

            return self::id($model->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new UpdateFailedException(static::message() . '.NAME');
        }
        throw new UpdateFailedException(static::message() . '.NAME');
    }

    /**
     * Check if functions in Base Helper is allowed to run
     * 
     * @param string $functionName
     * @return boolean $allowed
     */
    private static function checkAllowed(string $functionName)
    {
        $functionName = strtolower($functionName);
        $allowed = false;
        foreach (static::allowed() as $allowedName) {
            if(strtolower($allowedName) == $functionName) {
                $allowed = true;
                break;
            }
        }
        return $allowed;
    }
}