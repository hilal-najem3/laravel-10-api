<?php

namespace App\Containers\Common\Controllers;

use App\Http\Controllers\Controller;

use App\Helpers\Response\ResponseHelper;

use App\Containers\Common\Helpers\RegionsHelper as Helper;

use Exception;

class RegionsController extends Controller
{
    use ResponseHelper;
    
    public function all()
    {
        try {
            $regions = Helper::allCountries();

            $types = Helper::types();
            
            $info = [
                'types' => $types,
                'regions' => $regions
            ];
            return $this->response('REGIONS.ALL', $info);
        } catch (Exception $e) {
            return $this->errorResponse('REGIONS.ALL_FAILED', $e);
        }
        return $this->errorResponse('REGIONS.ALL_FAILED');
    }
}