<?php

namespace App\Containers\Agencies\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Agencies\Requests\DefaultCurrencyRequest;

use App\Containers\Agencies\Helpers\AgencyHelper;
use App\Containers\Currencies\Helpers\CurrenciesHelper;

use Exception;

class AgencyCurrenciesController extends Controller
{
    use ResponseHelper, PermissionControllersTrait;

    /**
     * Set default currency for agency
     * 
     * @param DefaultCurrencyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function defaultCurrency(DefaultCurrencyRequest $request)
    {
        try {
            $this->allowedAction(['write-agency-currency'], 'AGENCY.CREATE_FAILED');

            return $this->response('AGENCY.CREATE_SUCCESSFUL');

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY.CREATE_FAILED', $e);
        }

        return $this->errorResponse('AGENCY.CREATE_FAILED');
    }
}
