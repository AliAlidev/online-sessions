<?php

namespace App\Http\Requests\events;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'event_name' => 'required|string|unique:events,event_name',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'event_type' => 'required|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'customer' => 'required|string',
            'venue' => 'nullable|string',
            'active_duration' => 'nullable|string',
            'description' => 'nullable|string',
            'event_link' => 'required|url',
            'event_password' => 'nullable|string|min:6',
            'welcome_message' => 'nullable|string',
            'qr_code' => 'required|string',
            'accent_color' => 'required|string'
        ];
    }
}
