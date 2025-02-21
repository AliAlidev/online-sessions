<?php

namespace App\Http\Requests\users;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'name' => 'required|string',
            'full_name' => 'nullable|string',
            'phone' => 'required|string|max:20|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'permissions' => 'required|array',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ];
    }
}
