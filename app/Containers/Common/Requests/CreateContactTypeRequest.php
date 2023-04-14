<?php

namespace App\Containers\Common\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateContactTypeRequest extends FormRequest
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
            'name' => 'required|string|min:3|unique:contact_types,name',
            'allow_duplicates' => 'required|boolean',
            'regex' => 'nullable|string',
        ];
    }
}