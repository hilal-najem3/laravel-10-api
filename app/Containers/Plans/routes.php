<?php

use Illuminate\Support\Facades\Route;

use App\Containers\Plans\Controllers\PlansController as PC;
use App\Containers\Plans\Controllers\AgencyPlansController as APC;

use App\Containers\Roles\Models\Role;

Route::group([
    'prefix' => 'v1/plans',
    'middleware' => ['auth:api', 'roles:super-admin/admin']
], function ()
{
    Route::get('/get', [PC::class, 'get'])->name('plans.get');
    Route::get('/get/{id}', [PC::class, 'get'])->name('plans.get.id');

    Route::post('/create', [PC::class, 'create'])->name('plans.create');
    Route::put('/update/{id}', [PC::class, 'update'])->name('plans.update');
});

Route::group([
    'prefix' => 'v1/agency_plans',
    'middleware' => ['auth:api', 'roles:super-admin/admin']
], function ()
{
    Route::get('/get', [APC::class, 'get'])->name('agency.plans.get');
    Route::get('/get/{id}', [APC::class, 'get'])->name('agency.plans.get.id');

    Route::post('/create', [APC::class, 'create'])->name('agency.plans.create');
    Route::put('/update/{id}', [APC::class, 'update'])->name('agency.plans.update');
});