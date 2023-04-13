<?php

use Illuminate\Support\Facades\Route;
use App\Containers\Agencies\Controllers\AgencyTypesController;
use App\Containers\Agencies\Controllers\AgencyController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::group([
        'prefix' => 'agency',
        'middleware' => ['roles:super-admin/admin']
    ], function ()
    {
        Route::get('get', [AgencyController::class, 'get'])->name('agency.get');
        Route::get('get/{id}', [AgencyController::class, 'get'])->name('agency.get');
        Route::post('create', [AgencyController::class, 'create'])->name('agency.create');
        Route::put('update/{id}', [AgencyController::class, 'update'])->name('agency.update');
        Route::put('logo_update/{id}', [AgencyController::class, 'updateLogo'])->name('agency.update.logo');
    });

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