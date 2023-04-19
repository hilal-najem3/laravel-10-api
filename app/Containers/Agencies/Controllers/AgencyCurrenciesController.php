<?php

namespace App\Containers\Agencies\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Agencies\Traits\UserAgencyPermissionsTrait;
use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Agencies\Requests\DefaultCurrencyRequest;
use App\Containers\Agencies\Requests\UpdateActiveCurrencyConversion;

use App\Containers\Agencies\Helpers\AgencyHelper;
use App\Containers\Agencies\Helpers\AgencyCurrencyHelper;
use App\Containers\Currencies\Helpers\CurrenciesHelper;

use App\Containers\Agencies\Models\Agency;

use Exception;

class AgencyCurrenciesController extends Controller
{
    use ResponseHelper, PermissionControllersTrait, UserAgencyPermissionsTrait;

    /**
     * Get default currency for agency
     * 
     * @param int $agencyId
     * @return \Illuminate\Http\Response
     */
    public function getDefaultCurrency(int $agencyId)
    {
        try {
            $agency = AgencyHelper::id($agencyId);
            $defaultCurrency = AgencyCurrencyHelper::getDefaultCurrency($agency);

            return $this->response('AGENCY_CURRENCY.DEFAULT_GET', [
                'default_currency' => $defaultCurrency
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.DEFAULT_GET_FAILED', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.DEFAULT_GET_FAILED');
    }

    /**
     * Set default currency for agency
     * 
     * @param DefaultCurrencyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateDefaultCurrency(DefaultCurrencyRequest $request)
    {
        try {
            $this->allowedAction(['write-agency-currency'], 'AGENCY_CURRENCY.DEFAULT_NOT_ALLOWED');

            $agency = AgencyHelper::id($request->get('agency_id'));
            $this->allowAgencyUpdate($agency, 'AGENCY_CURRENCY.DEFAULT_NOT_ALLOWED');

            $currency = CurrenciesHelper::id($request->get('currency_id'));

            AgencyCurrencyHelper::updateDefaultCurrency($agency, $currency);

            return $this->response('AGENCY_CURRENCY.DEFAULT');

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.DEFAULT_FAILED', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.DEFAULT_FAILED');
    }

    /**
     * Get default currency for agency
     * 
     * @param int $agencyId
     * @return \Illuminate\Http\Response
     */
    public function getActiveCurrencyConversion(int $agencyId)
    {
        try {
            $agency = AgencyHelper::id($agencyId);
            $activeConversions = AgencyCurrencyHelper::getActiveConversion($agency);

            return $this->response('AGENCY_CURRENCY.CURRENCY_CONVERSION.GET', [
                'active_conversions' => $activeConversions
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.GET_NOT_FOUND', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.GET_NOT_FOUND');
    }

    /**
     * Add new currency conversion for an agency
     * 
     * @param UpdateActiveCurrencyConversion $request
     * @return \Illuminate\Http\Response
     */
    public function updateActiveCurrencyConversion(UpdateActiveCurrencyConversion $request)
    {
        try {
            $this->allowedAction(['write-agency-currency-conversion'], 'AGENCY_CURRENCY.CURRENCY_CONVERSION.UPDATE_NOT_ALLOWED');

            $data = $request->all();
            $agency = AgencyHelper::id($data['agency_id']);
            $this->allowAgencyUpdate($agency, 'AGENCY_CURRENCY.CURRENCY_CONVERSION.UPDATE_NOT_ALLOWED');

            $conversion = AgencyCurrencyHelper::updateActiveConversion($data);
            
            return $this->response('AGENCY_CURRENCY.CURRENCY_CONVERSION.UPDATE_ACTIVE_SUCCESSFUL', [
                'conversion' => $conversion    
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.UPDATE_ACTIVE_FAIL', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.UPDATE_ACTIVE_FAIL');
    }
}
