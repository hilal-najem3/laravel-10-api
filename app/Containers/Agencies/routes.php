<?php

use Illuminate\Support\Facades\Route;
use App\Containers\Agencies\Controllers\AgencyCurrenciesController;
use App\Containers\Agencies\Controllers\AgencyTypesController;
use App\Containers\Agencies\Controllers\AgencyController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::group([
        'prefix' => 'agency',
    ], function ()
    {
        Route::get('get', [AgencyController::class, 'get'])->name('agency.get');
        Route::get('get/{id}', [AgencyController::class, 'get'])->name('agency.get');

        Route::group([
            'prefix' => 'currencies',
        ], function ()
        {
            Route::get('default/get/{agencyId}', [AgencyCurrenciesController::class, 'getDefaultCurrency'])
            ->name('agency.currency.default.get');

            Route::get('conversions/get/{agencyId}', [AgencyCurrenciesController::class, 'getActiveCurrencyConversion'])
            ->name('agency.currency.conversion.get');
        });

        Route::group([
            'middleware' => ['roles:super-admin/admin']
        ], function ()
        {
            Route::post('create', [AgencyController::class, 'create'])->name('agency.create');
            Route::put('update/{id}', [AgencyController::class, 'update'])->name('agency.update');
            Route::post('logo/{id}', [AgencyController::class, 'logo'])->name('agency.update.logo');
        });

        Route::group([
            'middleware' => ['roles:super-admin/admin/agency-admin']
        ], function ()
        {
            Route::group([
                'prefix' => 'currencies',
            ], function ()
            {
                Route::post('default/update', [AgencyCurrenciesController::class, 'updateDefaultCurrency'])
                ->name('agency.currency.default.update');

                Route::post('conversions/set', [AgencyCurrenciesController::class, 'updateActiveCurrencyConversion'])
                ->name('agency.currency.conversion.set');
            });
        });
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