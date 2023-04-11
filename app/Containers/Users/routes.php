<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Containers\Users\Controllers\ProfileController;
use App\Containers\Users\Controllers\UsersController;

Route::group([
    'prefix' => 'v1',
    'middleware' => ['auth:api']
], function ()
{
    Route::group([
        'prefix' => 'profile'
    ], function () {
        // Profile
        Route::get('/', [ProfileController::class, 'get'])->name('profile.get');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'delete'])->name('profile.delete');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
        Route::post('/profile_image', [ProfileController::class, 'updatePhoto'])->name('profile.photo.upload');
        Route::put('/contact', [ProfileController::class, 'updateContact'])->name('profile.contact.update');
        Route::delete('/contact', [ProfileController::class, 'deleteContactData'])->name('profile.contact.delete');
    });
    
    Route::group([
        'prefix' => 'users',
        'middleware' => ['roles:super-admin/admin']
    ], function ()
    {
        // Users
        Route::get('get/', [UsersController::class, 'get'])->name('users.get');
        Route::get('get/{id}', [UsersController::class, 'id'])->name('users.get.id');
        Route::post('create/', [UsersController::class, 'create'])->name('users.create');
        Route::put('update/{id}', [UsersController::class, 'update'])->name('users.update.id');
        Route::put('contacts/{id}', [UsersController::class, 'updateContactData'])->name('users.update.contact');
        Route::delete('contacts/{id}', [UsersController::class, 'deleteContactData'])->name('users.delete.contacts');

        // Add / Remove permissions to user
        Route::put('addPermissionsToUser', [UsersController::class, 'addPermissionsToUser'])
        ->name('users.addPermissionsToUser');
        Route::put('removePermissionsToUser', [UsersController::class, 'removePermissionsToUser'])
        ->name('users.addPermissionsToUser');

        // Add / Remove roles to user
        Route::put('addRolesToUser', [UsersController::class, 'addRolesToUser'])
        ->name('users.addRolesToUser');
        Route::put('removeRolesToUser', [UsersController::class, 'removeRolesToUser'])
        ->name('users.addRolesToUser');

        // User activation and deactivation
        Route::get('inactiveUsers', [UsersController::class, 'getInActiveUsers'])->name('users.get.inactive');
        Route::put('deactivateUsers', [UsersController::class, 'deactivateUsers'])->name('users.deactivateUsers');
        Route::put('activateUsers', [UsersController::class, 'activateUsers'])->name('users.activateUsers');

        // Users delete
        Route::get('deletedUsers', [UsersController::class, 'getDeletedUsers'])->name('users.get.deleted');
        Route::delete('deleteUsers', [UsersController::class, 'deleteUsers'])->name('users.delete');
    });

});