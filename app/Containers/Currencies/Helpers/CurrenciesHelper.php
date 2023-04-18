<?php

namespace App\Containers\Currencies\Helpers;

use App\Containers\Currencies\Models\Currency;

use App\Helpers\BaseHelper;
    
class CurrenciesHelper extends BaseHelper
{
    protected static string $messageKeyBase = 'Currency';
    protected static string $modelName = 'Currency';
    protected static string $model = Currency::class;

    protected static function model()
    {
        return self::$model;
    }

    protected static function message()
    {
        return self::$messageKeyBase;
    }
}