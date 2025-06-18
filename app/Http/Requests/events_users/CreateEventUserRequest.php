<?php

namespace App\Http\Requests\events_users;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventUserRequest extends FormRequest
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
            'name' => ['required', 'string', 'unique:users,name', function ($attribute, $value, $fail) {
                if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('The name field must not be an email address.');
                }
            }],
            'full_name' => 'nullable|string',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'permissions' => 'nullable|array',
            'password' => 'required|confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'password_confirmation' => 'required',
            'user_type' => 'required|in:admin,client,event-user',
            'event_id' => 'required|exists:events,id'
        ];
    }

    function messages()
    {
        return [
            'password.regex' => "The password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one of those @$!%*?& special character."
        ];
    }
}
