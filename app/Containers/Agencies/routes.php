<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
});