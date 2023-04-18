<?php

namespace App\Containers\Currencies\Controllers;

use App\Http\Controllers\Controller;
use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Currencies\Helpers\CurrenciesHelper as Helper;

use Exception;

class CurrenciesController extends Controller
{
    use ResponseHelper, PermissionControllersTrait;

    /**
     * Get all currencies
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function get(int $id = null)
    {
        try {
            $data = [];

            if($id == null) {
                $currencies = Helper::all();
                $data = [
                    'currencies' => $currencies
                ];
            }

            if($id != null) {
                $currency = Helper::id($id);
                $data = [
                    'currency' => $currency
                ];
            }

            return $this->response('CURRENCY.GET', $data);

        } catch (Exception $e) {
            return $this->errorResponse('CURRENCY.NOT_FOUND', $e);
        }

        return $this->errorResponse('CURRENCY.NOT_FOUND');
    }
}