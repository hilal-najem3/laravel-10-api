<?php

namespace App\Containers\Users\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteUserContactDataRequest extends FormRequest
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
            'contact' => 'required',
            'contact.*.id' => 'nullable|integer|exists:contacts,id'
        ];
    }
}