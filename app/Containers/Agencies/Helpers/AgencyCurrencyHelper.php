<?php

namespace App\Containers\Agencies\Helpers;

use Illuminate\Support\Facades\DB;

use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Containers\Common\Helpers\DataHelper;

use App\Containers\Agencies\Models\Agency;
use App\Containers\Currencies\Models\Currency;

class AgencyCurrencyHelper
{
    /**
     * Get an Agency's default currency
     * 
     * @param  Agency $agency
     * @return Currency $currency | NotFoundException
     */
    public static function getDefaultCurrency(Agency $agency)
    {
        try {
            $agencyId = $agency->id;
            $currencyId = null;

            $defaultCurrencies = DataHelper::keyBase('default_currencies');
            if($defaultCurrencies) {
                $defaultCurrenciesFormattedValue = DataHelper::getValue($defaultCurrencies);
                if($defaultCurrenciesFormattedValue) {
                    foreach($defaultCurrenciesFormattedValue as $item) {
                        if($item['agency_id'] == $agencyId) {
                            $currencyId = $item['currency_id'];
                            break;
                        }
                    }
                }
            }
            if($currencyId == null) {
                throw new NotFoundException('AGENCY_CURRENCY.DEFAULT_NAME');
            }

            $currency = Currency::find($currencyId);
            if(!$currency) {
                throw new NotFoundException('AGENCY_CURRENCY.DEFAULT_NAME');
            }

            return $currency;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * update an Agency's default currency
     * 
     * @param  Agency $agency
     * @param  Currency $currency
     * @return boolean | UpdateFailedException
     */
    public static function updateDefaultCurrency(Agency $agency, Currency $currency)
    {
        DB::beginTransaction();
        try {
            $objectDataForDefaultCurrency = [
                'key' => 'default_currencies',
                'type_id' => DataHelper::getDataTypeBySlug('json')->id,
                'description' => 'This object is for all default currencies used by agencies',
            ];

            $currentDefault = [
                'agency_id' => $agency->id,
                'currency_id' => $currency->id
            ];

            $defaultCurrencies = DataHelper::keyBase('default_currencies');
            $defaultCurrenciesFormattedValue = [];

            if($defaultCurrencies) {
                $defaultCurrenciesFormattedValue = DataHelper::getValue($defaultCurrencies);
                $updated = false;
                foreach($defaultCurrenciesFormattedValue as $item) {
                    if($item['agency_id'] == $agency->id) {
                        $item = $currentDefault;
                        $updated = true;
                        break;
                    }
                }
                if(!$updated) {
                    array_push($defaultCurrenciesFormattedValue, $currentDefault);
                }

                $objectDataForDefaultCurrency['value'] = $defaultCurrenciesFormattedValue;
                DataHelper::update($defaultCurrencies, $objectDataForDefaultCurrency);
            } else {
                $defaultCurrenciesFormattedValue = [$currentDefault];
                $objectDataForDefaultCurrency['value'] = $defaultCurrenciesFormattedValue;
                DataHelper::create($objectDataForDefaultCurrency);
            }


            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            print_r($e->getMessage());
            throw new UpdateFailedException('AGENCY_CURRENCY.DEFAULT_FAILED');
        }
    }
}