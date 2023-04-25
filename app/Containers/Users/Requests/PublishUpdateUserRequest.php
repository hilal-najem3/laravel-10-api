<?php

namespace App\Containers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublishUpdateUserRequest extends FormRequest
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
            'first_name' => 'required|string|min:2',
            'last_name' => 'required|string|min:2',
            'email' => 'required|string|min:4|email',
            'dob' => 'nullable|date',
            'role_id' => 'nullable|integer|exists:roles,id',
            'phone' => 'nullable|string',
            'phone_hidden' => 'nullable|boolean',
            'address' => 'nullable|string',
            'full_address' => 'nullable',
            'full_address.country_id' => 'nullable|integer|exists:regions,id',
            'full_address.state_id' => 'nullable|integer|exists:regions,id',
            'full_address.city' => 'nullable|string',
            'full_address.street' => 'nullable|string',
            'full_address.building' => 'nullable|string',
            'full_address.floor' => 'nullable|string',
            'full_address.details' => 'nullable|string',
            'full_address.location' => 'nullable|string',
        ];
    }
}