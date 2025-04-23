<?php

namespace App\Http\Requests\users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $id = request()->route('id') ?? null;
        return [
            'name' => 'required|string',
            'full_name' => 'nullable|string',
            'phone' => 'nullable|string|max:20|unique:users,email,' . $id,
            'email' => 'nullable|email|unique:users,email,' . $id,
            'permissions' => 'nullable|array',
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
