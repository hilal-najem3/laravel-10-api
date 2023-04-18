<?php

namespace App\Helpers;

use App\Exceptions\Common\NotFoundException;
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