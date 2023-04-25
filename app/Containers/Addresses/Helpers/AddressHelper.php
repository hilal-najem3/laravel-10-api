<?php

namespace App\Containers\Addresses\Helpers;

use App\Containers\Addresses\Models\Address;

use App\Helpers\BaseHelper;

class AddressHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'ADDRESS';
    protected static string $modelName = 'Address';
    protected static string $model = Address::class;
    protected static $allowed = ['id', 'all', 'create', 'update'];

    protected static function model()
    {
        return self::$model;
    }

    protected static function message()
    {
        return self::$messageKeyBase;
    }

    protected static function allowed()
    {
        return self::$allowed;
    }
}