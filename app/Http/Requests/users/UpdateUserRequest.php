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
            'permissions' => 'required|array',
            'password' => 'nullable|confirmed',
            'password_confirmation' => 'nullable'
        ];
    }
}
