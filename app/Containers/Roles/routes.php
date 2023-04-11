<?php

use Illuminate\Support\Facades\Route;
use App\Containers\Roles\Controllers\RolesController;

Route::group([
    'prefix' => 'v1/roles',
    'middleware' => ['auth:api', 'roles:super-admin/admin']
], function ()
{
    Route::get('/', [RolesController::class, 'all'])->name('get.roles');
});