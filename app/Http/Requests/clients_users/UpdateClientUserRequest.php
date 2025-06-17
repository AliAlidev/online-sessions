<?php

namespace App\Http\Requests\clients_users;

use App\Models\Client;
use App\Models\UserClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class UpdateClientUserRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $clientUser = UserClient::find($this->client_user_id);
        $requiredPassword = $clientUser->client_id != $this->client_id ? "required|":"nullable|";
        $clientUser = UserClient::find($this->client_user_id);
        
        return [
            "client_user_id"=> "required",
            'client_id' => 'nullable|exists:clients,id|unique:users_clients,client_id,' . $request->client_user_id,
            'name' => 'nullable|string|unique:users,name,' . $clientUser->user->id,
            'password' => $requiredPassword . 'confirmed|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'password_confirmation' => 'nullable'
        ];
    }

    function messages()
    {
        return [
            'client_id.unique' => 'You already make this client as client user',
            'password.regex' => "The password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one of those @$!%*?& special character."
        ];
    }
}
