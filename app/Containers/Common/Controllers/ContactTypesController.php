<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Helpers\ContactTypesHelper as Helper;

use Exception;

class ContactTypesController extends Controller
{
    use ResponseHelper;

    /**
     * Get all contact types
     * or with id
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(int $id = null)
    {
        try {
            $info = [];

            if($id != null) {
                $type = Helper::id($id);
                $info = ['type' => $type];
            } else {
                $types = Helper::all();
                $info = ['types' => $types];
            }

            return $this->response(
                'CONTACT.TYPES',
                $info
            );
        } catch (Exception $e) {
            return $this->errorResponse('CONTACT.TYPES_ERROR', $e);
        }

        return $this->errorResponse('CONTACT.TYPES_ERROR');
    }
}