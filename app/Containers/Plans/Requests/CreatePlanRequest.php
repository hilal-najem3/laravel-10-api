<?php

namespace App\Containers\Plans\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlanRequest extends FormRequest
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
            'name' => 'required|string|max:191|unique:plans,name',
            'abbreviation' => 'nullable|string|max:100|unique:plans,abbreviation',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
        ];
    }
}