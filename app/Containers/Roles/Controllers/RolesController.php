<?php

namespace App\Containers\Roles\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\Response\ResponseHelper;
use App\Containers\Roles\Models\Role;

use Exception;

class RolesController extends Controller
{
    use ResponseHelper;
    
    public function all()
    {
        try {
            $info = [
                'roles' => Role::all(),
            ];
            return $this->response('ROLES.ALL', $info);
        } catch (Exception $e) {
            return $this->errorResponse('ROLES.ALL_FAILED', $e);
        }
        return $this->errorResponse('ROLES.ALL_FAILED');
    }
}