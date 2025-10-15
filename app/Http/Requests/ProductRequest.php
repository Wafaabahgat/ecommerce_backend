<?php

namespace App\Http\Requests;

use App\Http\Helpers\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            "store_id" => ['required', 'exists:stores,id'],
            "category_id" => ['nullable', 'exists:categories,id'],
            "name" => ['required', 'string', 'min:2', 'max:100', Rule::unique('products', 'name')->ignore($this->product)],
            "disc" => ['nullable', 'string', 'min:2', 'max:255'],
            "image" => ['nullable', 'image', 'mimes:png,jpg,webp'],
            "price" => ['required', 'numeric'],
            "compare_price" => ['nullable', 'numeric'],
            "rating" => ['nullable', 'numeric'],
            "options" => ['nullable'],
            "type" => ['nullable', 'string', 'in:hot,new,top_rated,best_selling'],
            "status" => ['sometimes', 'string', 'in:active,disactive'],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        Helper::sendError('validation error', $validator->errors());
    }
}
