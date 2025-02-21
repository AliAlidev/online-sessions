<?php

namespace App\Http\Requests\clients;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $id = request()->get('client_id') ?? null;
        return [
            'client_id' => 'required|exists:clients,id',
            'planner_name' => 'required|string',
            'planner_business_name' => 'nullable|string',
            'phone_number' => 'required|string|max:20|unique:clients,phone_number,' . $id,
            'email' => 'required|email|unique:clients,email,' . $id,
            'role' => 'required|string|exists:client_roles,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'contact_button_text' => 'required|string',
            'contact_button_link' => 'required|url',
            'description' => 'nullable|string'
        ];
    }
}
