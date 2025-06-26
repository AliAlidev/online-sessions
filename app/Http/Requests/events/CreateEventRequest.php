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
            'event_alias_name' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'event_type_id' => 'required|exists:event_types,id',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'client_id' => 'required|exists:clients,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'customer' => 'nullable|string',
            'venue' => 'nullable|string',
            'active_duration' => 'nullable|string',
            'description' => 'nullable|string',
            'event_link' => 'required|url',
            'event_password' => 'nullable',
            'welcome_message' => 'nullable|string',
            'qr_code' => 'required|string',
            'accent_color' => 'nullable|string',
            'organizers' => isset($this->enable_organizer) ? 'required|array' : '',
            'organizers.*.organizer_id' => isset($this->enable_organizer) ? 'required|exists:vendors,id' : '',
            'organizers.*.role_in_event' => isset($this->enable_organizer) ? 'required|string' : '',
            'image_share_guest_book' => 'nullable',
            'image_folders' => 'nullable',
            'video_playlist' => 'nullable',
            'allow_upload' => 'nullable',
            'auto_image_approve' => 'nullable',
            'allow_image_download' => 'nullable',
            'theme' => 'nullable',
            'font' => 'nullable',
            'enable_organizer' => 'nullable',
            'page_length' => 'nullable'
        ];
    }
}
