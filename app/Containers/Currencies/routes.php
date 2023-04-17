<?php

use Illuminate\Support\Facades\Route;

use App\Containers\Auth\Controllers\PassportAuthController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    
});