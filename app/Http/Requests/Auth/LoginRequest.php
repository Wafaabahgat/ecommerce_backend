<?php

namespace App\Http\Requests\Auth;

use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' =>  'required|email|max:200|exists:users,email',
            'password' => 'required|min:6',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        Helper::sendError('validation error', $validator->errors());
    }
}
