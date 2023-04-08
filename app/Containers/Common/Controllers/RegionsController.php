<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Helpers\RegionsHelper as Helper;

use App\Containers\Common\Helpers\MessagesHelper;

use Exception;

class RegionsController extends Controller
{
    use ResponseHelper;

    protected $messages = array();

    public function __construct()
    {
        $this->messages = MessagesHelper::messages();
    }

    public function all()
    {
        try {
            $regions = Helper::allCountries();

            $types = Helper::types();
            
            $info = [
                'types' => $types,
                'regions' => $regions
            ];
            return $this->return_response(
                $this->success,
                $info,
                $this->messages['REGIONS']['ALL']
            );
        } catch (Exception $e) {
            return $this->return_response($this->bad_request, [], $this->messages['REGIONS']['ALL_FAILED'], $e->getMessage());
        }

        return $this->return_response($this->bad_request, [], $this->messages['REGIONS']['ALL_FAILED']);
    }
}