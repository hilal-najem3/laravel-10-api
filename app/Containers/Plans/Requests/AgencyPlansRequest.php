<?php

namespace App\Containers\Plans\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgencyPlansRequest extends FormRequest
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
            'plan_id' => 'required|integer|exists:plans,id',
            'agency_id' => 'required|integer|exists:agencies,id',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date',
            'active' => 'nullable|boolean',
            'note' => 'nullable|string',
        ];
    }
}