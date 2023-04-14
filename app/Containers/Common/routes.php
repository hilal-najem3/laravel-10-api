<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Containers\Common\Controllers\RegionsController;
use App\Containers\Common\Controllers\ContactTypesController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::get('regions', [RegionsController::class, 'all'])->name('allRegions&Types.get');

    Route::prefix('contact_types')
    ->group(function () {
        Route::get('/get', [ContactTypesController::class, 'get'])->name('contact_types.get');
        Route::get('/get/{id}', [ContactTypesController::class, 'get'])->name('contact_types.get');

        Route::group([
            'middleware' => ['roles:super-admin/admin']
        ], function ()
        {
            Route::post('/create', [ContactTypesController::class, 'create'])->name('contact_types.create');
        });
    });
});