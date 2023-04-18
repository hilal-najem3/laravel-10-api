<?php

namespace App\Helpers;

use App\Exceptions\Common\NotFoundException;
use Exception;

use Illuminate\Database\Eloquent\Model;

abstract class BaseHelper
{
    abstract protected static function model();
    abstract protected static function message();

    /**
     * get model's base info
     * 
     * @param int $id
     * @return Model
     */
    public static function id(int $id)
    {
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
}