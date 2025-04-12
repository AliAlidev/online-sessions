<?php

namespace App\Http\Requests\event_types;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = request()->get('event_type_id')?? null;
        return [
            'event_type_id' => 'required|exists:event_types,id',
            'name' => 'required|unique:event_types,name,' . $id
        ];
    }
}
