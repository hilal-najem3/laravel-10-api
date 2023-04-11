<?php

use Illuminate\Support\Facades\Route;
use App\Containers\Permissions\Controllers\PermissionsController;

Route::group([
    'prefix' => 'v1/permissions',
    'middleware' => ['auth:api', 'roles:super-admin']
], function ()
{
    Route::get('/', [PermissionsController::class, 'all'])->name('get.permissions');
});