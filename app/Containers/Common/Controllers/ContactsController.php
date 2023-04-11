<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Helpers\ContactHelper as Helper;

use Exception;

class ContactsController extends Controller
{
    use ResponseHelper;

    public function contactTypes()
    {
        try {
            $types = Helper::types();
            
            $info = [
                'types' => $types,
            ];
            return $this->response(
                'CONTACT.TYPES',
                $info
            );
        } catch (Exception $e) {
            return $this->errorResponse($this->bad_request, 'CONTACT.TYPES_ERROR', $e);
        }

        return $this->errorResponse($this->bad_request, 'CONTACT.TYPES_ERROR');
    }
}