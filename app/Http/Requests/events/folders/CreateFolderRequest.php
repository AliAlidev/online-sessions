<?php

namespace App\Http\Requests\events\folders;

use Illuminate\Foundation\Http\FormRequest;

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
        if ($this->folder_type != 'link')
            $link = 'nullable';

        return [
            'folder_name' => 'required|string',
            'folder_type' => 'required|in:image,video,link',
            'description' => 'nullable|string',
            'folder_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'folder_link' => $link
        ];
    }
}
