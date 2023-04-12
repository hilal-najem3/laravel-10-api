<?php

namespace App\Containers\Agencies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAgencyTypeRequest extends FormRequest
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
            'name' => 'required|string|min:3|unique:agency_types,name',
            'description' => 'nullable|string|min:10'
        ];
    }
}