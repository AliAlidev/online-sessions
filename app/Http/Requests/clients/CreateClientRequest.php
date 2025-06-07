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
            'email' => 'required|email|unique:users,email',
            'client_role' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'password' => 'required|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'password_confirmation' => 'required'
        ];
    }

    function messages()
    {
        return [
            'password.regex' => "The password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one of those @$!%*?& special character."
        ];
    }
}
