<?php

namespace App\Http\Requests\events\folders;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'folder_id' => 'required|exists:event_folders,id',
            'folder_name' => 'required|string|unique:event_folders,folder_name,' . $id,
            'folder_type' => 'required|in:image,video,link',
            'description' => 'nullable|string',
            'folder_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'folder_link' => 'nullable|url'
        ];
    }
}
