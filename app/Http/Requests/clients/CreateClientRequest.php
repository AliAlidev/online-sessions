<?php

namespace App\Http\Requests\clients;

use Illuminate\Foundation\Http\FormRequest;

class CreateClientRequest extends FormRequest
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
        return [
            'planner_name' => 'required|string',
            'planner_business_name' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20|unique:users,phone',
            'email' => 'nullable|email|unique:users,email',
            'client_role' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
        ];
    }
}
