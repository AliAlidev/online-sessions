<?php

namespace App\Http\Requests\events\files;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
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
            'file_id' => 'required|exists:folder_files,id',
            'file' => 'nullable|file',
            'user_name' => 'nullable',
            'file_type'=>'nullable',
            'file_size'=>'nullable',
            'description'=>'nullable',
            'file_status'=>'required',
            'video_resolution' => 'nullable',
            'video_name' => 'nullable',
            'file_order' => 'nullable'
        ];
    }
}
