<?php

namespace App\Containers\Permissions\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\Response\ResponseHelper;
use App\Containers\Permissions\Models\Permission;

use Exception;

class PermissionsController extends Controller
{
    use ResponseHelper;
    
    public function all()
    {
        try {
            $info = [
                'permissions' => Permission::all(),
            ];
            return $this->response('PERMISSIONS.ALL', $info);
        } catch (Exception $e) {
            return $this->errorResponse('PERMISSIONS.ALL_FAILED', $e);
        }
        return $this->errorResponse('PERMISSIONS.ALL_FAILED');
    }
}