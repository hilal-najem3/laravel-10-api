<?php

namespace App\Containers\Agencies\Controllers;

use App\Http\Controllers\Controller;

use App\Containers\Agencies\Traits\UserAgencyPermissionsTrait;
use App\Containers\Common\Traits\PermissionControllersTrait;
use App\Helpers\Response\ResponseHelper;

use App\Containers\Agencies\Requests\DefaultCurrencyRequest;
use App\Containers\Agencies\Requests\CurrencyConversionRequest;
use App\Requests\PaginationRequest;

use App\Containers\Agencies\Helpers\AgencyHelper;
use App\Containers\Agencies\Helpers\AgencyCurrencyHelper;
use App\Containers\Currencies\Helpers\CurrenciesHelper;

use App\Exceptions\Common\NotFoundException;

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
     * Add new active currency conversion for an agency
     * 
     * @param CurrencyConversionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function updateActiveCurrencyConversion(CurrencyConversionRequest $request)
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

    /**
     * Get conversions history for agency
     * 
     * @param PaginationRequest $request
     * @param int $agencyId
     * @param int $conversionId
     * @return \Illuminate\Http\Response
     */
    public function getConversionsHistory(PaginationRequest $request, int $agencyId, int $conversionId = null)
    {
        try {
            $this->allowedAction(['crud-agency-conversions-history'], 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $agency = AgencyHelper::id($agencyId);
            $this->allowAgencyUpdate($agency, 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $info = [];
            if($conversionId != null) {
                $conversion = $agency->conversionsHistory()->where('id', $conversionId)->first();
                $conversion != null ? 
                $info = ['conversion' => $conversion] : 
                throw new NotFoundException('CONVERSION_HISTORY.NAME');
            } else {
                $data = AgencyCurrencyHelper::getConversionHistory($agency, $request->get('pagination'));
                $info = [
                    'meta' => $this->metaData($data),
                    'conversions' => $data->data
                ];
            }

            return $this->response('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_GET', $info);

        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_FAIL', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_FAIL');
    }

    /**
     * Add new currency conversion for an agency this can be old
     * this uses the same request as active conversion but add the record
     * in history table only
     * 
     * @param CurrencyConversionRequest $request
     * @return \Illuminate\Http\Response
     */
    public function addConversionsHistory(CurrencyConversionRequest $request)
    {
        try {
            $this->allowedAction(['crud-agency-conversions-history'], 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $data = $request->all();
            $agency = AgencyHelper::id($data['agency_id']);
            $this->allowAgencyUpdate($agency, 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $conversion = AgencyCurrencyHelper::createConversion($data);

            return $this->response('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_CREATE', ['conversion' => $conversion]);
        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_CREATE_FAIL', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_CREATE_FAIL');
    }

    /**
     * Add new currency conversion for an agency this can be old
     * this uses the same request as active conversion but add the record
     * in history table only
     * 
     * @param CurrencyConversionRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateConversionsHistory(CurrencyConversionRequest $request, int $id)
    {
        try {
            $this->allowedAction(['crud-agency-conversions-history'], 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $data = $request->all();
            $agency = AgencyHelper::id($data['agency_id']);
            $this->allowAgencyUpdate($agency, 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $conversion = $agency->conversionsHistory()->where('id', $id)->first();
            $conversion = AgencyCurrencyHelper::updateConversion($conversion, $data);

            return $this->response('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_CREATE', ['conversion' => $conversion]);
        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_CREATE_FAIL', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_CREATE_FAIL');
    }

    /**
     * Delete a currency conversion
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteConversionsHistory(int $id)
    {
        try {
            $this->allowedAction(['crud-agency-conversions-history'], 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            $conversion = AgencyCurrencyHelper::getConversionHistoryById($id);
            $agency = AgencyHelper::id($conversion->agency_id);
            $this->allowAgencyUpdate($agency, 'AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_NOT_ALLOWED');

            AgencyCurrencyHelper::deleteConversionHistory($conversion);

            return $this->response('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_DELETE');
        } catch (Exception $e) {
            return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_DELETE_FAIL', $e);
        }

        return $this->errorResponse('AGENCY_CURRENCY.CURRENCY_CONVERSION.HISTORY_DELETE_FAIL');
    }
}
