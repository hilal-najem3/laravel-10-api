<?php

namespace App\Containers\Agencies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DefaultCurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'agency_id' => 'required|integer|exists:agencies,id',
            'currency_id' => 'required|integer|exists:currencies,id'
        ];
    }
}