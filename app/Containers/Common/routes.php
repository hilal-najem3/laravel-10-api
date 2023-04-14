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
    Route::get('regions', [RegionsController::class, 'all'])->name('get.allRegions&Types');

    Route::prefix('contact_types')
    ->group(function () {
        Route::get('/get', [ContactTypesController::class, 'get'])->name('get.contact_types');
        Route::get('/get/{id}', [ContactTypesController::class, 'get'])->name('get.contact_types');
    });
});