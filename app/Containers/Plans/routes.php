<?php

use Illuminate\Support\Facades\Route;

use App\Containers\Plans\Controllers\PlansController as PC;

Route::group([
    'prefix' => 'v1/plans',
    'middleware' => ['auth:api', 'roles:super-admin/admin']
], function ()
{
    Route::get('/get', [PC::class, 'get'])->name('plans.get');
    Route::get('/get/{id}', [PC::class, 'get'])->name('plans.get.id');
});