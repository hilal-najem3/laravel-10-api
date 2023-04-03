<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Containers\Common\Controllers\RegionsController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::get('regions/all', [RegionsController::class, 'all'])->name('get.allRegions&Types');
});