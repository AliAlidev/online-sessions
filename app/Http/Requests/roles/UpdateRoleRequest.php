<?php

namespace App\Http\Requests\roles;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
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
        $id = $this->get('role_id') ?? null;
        return [
            'role_id' => 'required|exists:client_roles,id' ,
            'name' => 'required|unique:client_roles,name,' . $id
        ];
    }
}
