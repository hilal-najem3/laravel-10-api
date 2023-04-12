<?php

use Illuminate\Support\Facades\Route;
use App\Containers\Agencies\Controllers\AgencyTypesController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::group([
        'prefix' => 'agency_type',
        'middleware' => ['roles:super-admin/admin']
    ], function ()
    {
        Route::get('get', [AgencyTypesController::class, 'get'])->name('agency_type.get');
        Route::get('get/{id}', [AgencyTypesController::class, 'get'])->name('agency_type.get');
        Route::post('create', [AgencyTypesController::class, 'create'])->name('agency_type.create');
        Route::put('update/{id}', [AgencyTypesController::class, 'update'])->name('agency_type.update');
    });
});