<?php

use Illuminate\Support\Facades\Route;

use App\Containers\Currencies\Controllers\CurrenciesController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::get('currencies', [CurrenciesController::class, 'get'])->name('currencies.get');
    Route::get('currencies/{id}', [CurrenciesController::class, 'get'])->name('currencies.get.id');
});