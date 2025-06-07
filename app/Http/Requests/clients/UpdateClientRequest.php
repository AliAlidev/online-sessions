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
            'phone_number' => 'nullable|string|max:20|unique:clients,phone_number,' . $id,
            'email' => 'nullable|email|unique:clients,email,' . $id,
            'client_role' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'password' => 'nullable|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'password_confirmation' => 'nullable'
        ];
    }

    function messages()
    {
        return [
            'password.regex' => "The password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one of those @$!%*?& special character."
        ];
    }
}
