<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here we mention every route file added within every container
|
*/

require __DIR__ . '/Auth/routes.php';
require __DIR__ . '/Agencies/routes.php';
require __DIR__ . '/Common/routes.php';
require __DIR__ . '/Currencies/routes.php';
require __DIR__ . '/Roles/routes.php';
require __DIR__ . '/Permissions/routes.php';
require __DIR__ . '/Users/routes.php';
