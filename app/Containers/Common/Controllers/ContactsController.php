<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Helpers\ContactHelper as Helper;

use App\Containers\Common\Helpers\MessagesHelper;

use Exception;

class ContactsController extends Controller
{
    use ResponseHelper;

    protected $messages = array();

    public function __construct()
    {
        $this->messages = MessagesHelper::messages();
    }

    public function contactTypes()
    {
        try {
            $types = Helper::types();
            
            $info = [
                'types' => $types,
            ];
            return $this->return_response(
                $this->success,
                $info,
                $this->messages['CONTACT']['TYPES']
            );
        } catch (Exception $e) {
            return $this->return_response($this->bad_request, [], $this->messages['CONTACT']['TYPES_ERROR'], $e->getMessage());
        }

        return $this->return_response($this->bad_request, [], $this->messages['CONTACT']['TYPES_ERROR']);
    }
}