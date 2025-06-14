<?php

namespace App\Http\Requests\events\folders;

use App\Models\EventFolder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFolderRequest extends FormRequest
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
        $id = request()->get('folder_id') ?? null;
        $eventId = EventFolder::find($id)->event_id;
        $link = 'required|url';
        if ($this->folder_type != 'link')
            $link = 'nullable';

        $name = [
            'required',
            'string',
            Rule::unique('event_folders', 'folder_name')->where(function ($query) use ($eventId) {
                return $query->where('event_id', $eventId);
            })->ignore($id)
        ];
        if ($this->folder_type == 'fake')
            $name = 'nullable';
        return [
            'folder_id' => 'required|exists:event_folders,id',
            'folder_name' => $name,
            'folder_type' => (getUserType() != 'event-user' ? 'required|' : null) . 'in:image,video,link,fake',
            'description' => 'nullable|string',
            'folder_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'folder_link' => $link,
            'order' => 'required|numeric'
        ];
    }
}
