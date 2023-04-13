<?php

namespace App\Containers\Agencies\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgencyLogoRequest extends FormRequest
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
            'logo' => 'nullable|mimes:jpeg,png,jpg'
        ];
    }
}