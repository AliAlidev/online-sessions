<?php

namespace App\Http\Requests\events;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
        $id = request()->route('id');
        $event = Event::find($id);
        if ($event && $event->canUpdateEventNameAndStartDate() && !isClientUser()) {
            $eventValidation = 'required|string|unique:events,event_name,' . $id;
            $eventStartDateValidation = 'required|date';
        } else {
            $eventValidation = '';
            $eventStartDateValidation = '';
        }
        return [
            'event_id' => 'required|exists:events,id',
            'event_name' =>  $eventValidation,
            'event_alias_name' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'event_type_id' => !isClientUser() ? 'required|exists:event_types,id' : '',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'client_id' => !isClientUser() ? 'required|exists:clients,id' : '',
            'start_date' => $eventStartDateValidation,
            'end_date' => !isClientUser() ? 'required|date|after_or_equal:start_date' : '',
            'customer' => 'nullable|string',
            'venue' => 'nullable|string',
            'active_duration' => 'nullable|string',
            'description' => 'nullable|string',
            'event_link' => !isClientUser() ? 'required|url' : '',
            'event_password' => 'nullable',
            'welcome_message' => 'nullable|string',
            'qr_code' =>  !isClientUser() ? 'required|string' : '',
            'accent_color' => 'nullable|string',
            'organizers' => !isClientUser() ? 'required|array' : '',
            'organizers.*.organizer_id' => !isClientUser() && isset($this->enable_organizer) ? 'required|exists:vendors,id' : '',
            'organizers.*.role_in_event' => !isClientUser() && isset($this->enable_organizer) ? 'required|string' : '',
            'organizers.*.organizer_model_id' => isset($this->enable_organizer) ? 'nullable|exists:event_organizers,id' : '',
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
