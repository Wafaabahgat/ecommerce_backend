<?php

namespace App\Http\Requests;

use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
            'parent_id' => [
                'nullable', 'int', 'exists:categories,id'
            ],
            "name" => ['required', 'string', 'min:2', 'max:100', Rule::unique('categories', 'name')->ignore($this->category)],
            "disc" => ['nullable', 'string', 'min:3', 'max:255'],
            "image" => ['nullable', 'image', 'mimes:png,jpg,webp'],
            "status" => ['nullable', 'string', 'in:active,disactive'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        Helper::sendError('validation error', $validator->errors());
    }
}
