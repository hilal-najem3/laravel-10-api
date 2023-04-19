<?php

namespace App\Containers\Agencies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActiveCurrencyConversion extends FormRequest
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
            'from' => 'required|integer|exists:currencies,id',
            'to' => 'required|integer|exists:currencies,id',
            'ratio' => 'required|numeric|min:0',
            'operation' => 'required|string',
            'date_time' => 'required|date'
        ];
    }
}