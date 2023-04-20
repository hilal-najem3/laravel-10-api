<?php

use Illuminate\Support\Facades\Route;
use App\Containers\Agencies\Controllers\AgencyCurrenciesController as ACC;
use App\Containers\Agencies\Controllers\AgencyTypesController as ATC;
use App\Containers\Agencies\Controllers\AC as AC;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::group([
        'prefix' => 'agency',
    ], function ()
    {
        Route::get('get', [AC::class, 'get'])->name('agency.get');
        Route::get('get/{id}', [AC::class, 'get'])->name('agency.get');

        Route::group([
            'prefix' => 'currencies',
        ], function ()
        {
            Route::get('default/get/{agencyId}', [ACC::class, 'getDefaultCurrency'])
            ->name('agency.currency.default.get');

            Route::get('conversions/get/{agencyId}', [ACC::class, 'getActiveCurrencyConversion'])
            ->name('agency.currency.conversion.get');
        });

        Route::group([
            'middleware' => ['roles:super-admin/admin']
        ], function ()
        {
            Route::post('create', [AC::class, 'create'])->name('agency.create');
            Route::put('update/{id}', [AC::class, 'update'])->name('agency.update');
            Route::post('logo/{id}', [AC::class, 'logo'])->name('agency.update.logo');
        });

        Route::group([
            'middleware' => ['roles:super-admin/admin/agency-admin']
        ], function ()
        {
            Route::group([
                'prefix' => 'currencies',
            ], function ()
            {
                Route::post('default/update', [ACC::class, 'updateDefaultCurrency'])
                ->name('agency.currency.default.update');

                Route::post('conversions/set', [ACC::class, 'updateActiveCurrencyConversion'])
                ->name('agency.currency.conversion.set');

                Route::get('conversions/history/{agencyId}', [ACC::class, 'getConversionsHistory'])
                ->name('agency.currency.conversion.history.get');
                Route::get('conversions/history/{agencyId}/{conversionId}', [ACC::class, 'getConversionsHistory'])
                ->name('agency.currency.conversion.history.get');
            });
        });
    });

    Route::group([
        'prefix' => 'agency_type',
        'middleware' => ['roles:super-admin/admin']
    ], function ()
    {
        Route::get('get', [ATC::class, 'get'])->name('agency_type.get');
        Route::get('get/{id}', [ATC::class, 'get'])->name('agency_type.get');
        Route::post('create', [ATC::class, 'create'])->name('agency_type.create');
        Route::put('update/{id}', [ATC::class, 'update'])->name('agency_type.update');
    });
});