<?php

namespace App\Containers\Agencies\Helpers;

use Illuminate\Support\Facades\DB;

use App\Exceptions\Common\UpdateFailedException;
use App\Exceptions\Common\CreateFailedException;
use App\Exceptions\Common\DeleteFailedException;
use App\Exceptions\Common\NotFoundException;
use Exception;

use App\Containers\Common\Helpers\DataHelper;

use App\Containers\Agencies\Models\Agency;
use App\Containers\Currencies\Models\Currency;
use App\Containers\Currencies\Models\CurrencyConversion;
use App\Containers\Currencies\Models\CurrencyConversionHistory;

use App\Helpers\Response\CollectionsHelper;
use App\Helpers\ConstantsHelper;

use Carbon\Carbon;

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
            throw new UpdateFailedException('AGENCY_CURRENCY.DEFAULT_FAILED');
        }
    }

    /**
     * Get an Agency's currency conversions that are active
     * 
     * @param  Agency $agency
     * @return CurrencyConversion[] $conversions
     */
    public static function getActiveConversion(Agency $agency)
    {
        $conversions = $agency->currentConversions()->get();

        return $conversions;
    }

    /**
     * This function receives data for a currency conversions for it
     * so it either create or updates an already existing currency conversion with
     * the same agency_id, from and to data.
     * And this function updates the conversions_history table with a new record automatically
     * 
     * @param array $data
     * @return CurrencyConversion $conversion | UpdateFailedException
     */
    public static function updateActiveConversion(array $data)
    {
        DB::beginTransaction();
        try {
            $from = Currency::find($data['from']);
            $to = Currency::find($data['to']);

            if(!$from || !$to) {
                throw new UpdateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.WRONG_CURRENCIES');
            }

            if($from == $to) {
                throw new UpdateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.SAME_CURRENCIES');
            }

            $op = $data['operation'];
            if($op != '*' && $op != '/') {
                throw new UpdateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.INVALID_OPERATION');
            }

            $conversion = CurrencyConversion::where([
                ['agency_id', $data['agency_id']],
                ['from', $from->id],
                ['to', $to->id]
            ])->get()->first();

            if(!$conversion) {
                $conversion = CurrencyConversion::create($data);
            } else {
                $conversion->operation = $data['operation'];
                $conversion->ratio = $data['ratio'];
                $conversion->date_time = new Carbon($data['date_time']);
                $conversion->save();
            }

            CurrencyConversionHistory::create($data);
            DB::commit();

            return CurrencyConversion::find($conversion->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get an Agency's currency conversions history
     * 
     * @param  Agency $agency
     * @param int $paginationCount
     * @return pagination of CurrencyConversionHistory[]
     */
    public static function getConversionHistory(Agency $agency, int $paginationCount = null)
    {
        $paginationCount = ConstantsHelper::getPagination($paginationCount);
        $conversions = $agency->conversionsHistory()->orderBy('date_time', 'DESC')->get();
        $conversions = CollectionsHelper::paginate($conversions, $paginationCount);
        $conversions = json_decode(json_encode($conversions)); // This will change its type to StdClass
        return $conversions;
    }

    /**
     * Get an currency conversions history by id
     * 
     * @param int $id
     * @return CurrencyConversionHistory $conversions | NotFoundException
     */
    public static function getConversionHistoryById(int $id): CurrencyConversionHistory
    {
        $conversion = CurrencyConversionHistory::find($id);

        if(!$conversion){
            throw new NotFoundException('CONVERSION_HISTORY.NAME');
        }

        return $conversion;
    }

    /**
     * This function receives data for a currency conversions for it
     * so it either create a conversion history record for an agency
     * 
     * @param array $data
     * @return CurrencyConversionHistory $conversion | CreateFailedException
     */
    public static function createConversion(array $data)
    {
        DB::beginTransaction();
        try {
            $from = Currency::find($data['from']);
            $to = Currency::find($data['to']);
            if(!$from || !$to) {
                throw new CreateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.WRONG_CURRENCIES');
            }
            if($from == $to) {
                throw new CreateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.SAME_CURRENCIES');
            }
            $op = $data['operation'];
            if($op != '*' && $op != '/') {
                throw new CreateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.INVALID_OPERATION');
            }

            $conversion = CurrencyConversionHistory::create($data);
            DB::commit();

            return CurrencyConversionHistory::find($conversion->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * This function receives data for a currency conversions for it
     * so it either create a conversion history record for an agency
     * 
     * @param CurrencyConversionHistory $conversion
     * @param array $data
     * @return CurrencyConversionHistory $conversion | UpdateFailedException
     */
    public static function updateConversion(CurrencyConversionHistory $conversion, array $data)
    {
        DB::beginTransaction();
        try {
            $from = Currency::find($data['from']);
            $to = Currency::find($data['to']);
            if(!$from || !$to) {
                throw new UpdateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.WRONG_CURRENCIES');
            }
            if($from == $to) {
                throw new UpdateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.SAME_CURRENCIES');
            }
            $op = $data['operation'];
            if($op != '*' && $op != '/') {
                throw new UpdateFailedException('', 'AGENCY_CURRENCY.CURRENCY_CONVERSION.INVALID_OPERATION');
            }

            $conversion->from = $data['from'];
            $conversion->to = $data['to'];
            $conversion->ratio = $data['ratio'];
            $conversion->operation = $data['operation'];
            $conversion->date_time = new Carbon($data['date_time']);
            $conversion->save();
            DB::commit();

            return CurrencyConversionHistory::find($conversion->id);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * This function receives data for a currency conversions for it
     * so it either create a conversion history record for an agency
     * 
     * @param CurrencyConversionHistory $conversion
     * @return boolean | DeleteFailedException
     */
    public static function deleteConversionHistory(CurrencyConversionHistory $conversion)
    {
        DB::beginTransaction();
        try {
            $conversion->delete();
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new DeleteFailedException('CONVERSION_HISTORY.NAME');
        }
    }
}