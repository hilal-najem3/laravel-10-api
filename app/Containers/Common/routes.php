<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Containers\Common\Controllers\RegionsController;
use App\Containers\Common\Controllers\ContactsController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::get('regions', [RegionsController::class, 'all'])->name('get.allRegions&Types');

    Route::get('contact_types', [ContactsController::class, 'contactTypes'])->name('get.contactTypes');
});