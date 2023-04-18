<?php

use Illuminate\Support\Facades\Route;

use App\Containers\Currencies\Controllers\CurrenciesController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::get('currencies/get', [CurrenciesController::class, 'get'])->name('currencies.get');
    Route::get('currencies/get/{id}', [CurrenciesController::class, 'get'])->name('currencies.get.id');
});