<?php

namespace App\Http\Requests\events\folders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateFolderRequest extends FormRequest
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
        $link = 'required|url';
        $name = [
            'required',
            'string',
            Rule::unique('event_folders', 'folder_name')->where(function ($query) {
                return $query->where('event_id', $this->route('event_id'));
            }),
        ];

        if ($this->folder_type != 'link' || $this->folder_type != 'fake')
            $link = 'nullable';

        if ($this->folder_type == 'fake')
            $name = 'nullable';

        return [
            'folder_name' => $name,
            'folder_type' => 'required|in:image,video,link,fake',
            'description' => 'nullable|string',
            'folder_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'folder_link' => $link,
            'order' => 'required|numeric'
        ];
    }
}
