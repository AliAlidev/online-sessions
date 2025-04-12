<?php

namespace App\Http\Requests\events\files;

use Illuminate\Foundation\Http\FormRequest;

class CreateFileRequest extends FormRequest
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
            'user_name' => 'nullable',
            'file' => 'required|file',
            'file_size' => 'nullable',
            'file_type' => 'nullable',
            'description' => 'nullable',
            'file_status' => 'nullable',
            'video_resolution' => 'nullable'
        ];
    }
}
