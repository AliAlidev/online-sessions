<?php

namespace App\Http\Requests\vendors;

use Illuminate\Foundation\Http\FormRequest;

class CreateVendorRequest extends FormRequest
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
            'vendor_name' => 'required|string',
            'phone_number' => 'nullable|string|max:20|unique:vendors,phone_number',
            'email' => 'nullable|email|unique:vendors,email',
            'vendor_role' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'contact_button_text' => 'required|string',
            'contact_button_link' => 'nullable|url'
        ];
    }
}
