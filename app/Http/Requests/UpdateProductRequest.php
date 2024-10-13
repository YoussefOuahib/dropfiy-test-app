<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes','required' ,'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'sku' => [
                'sometimes',
                'required',
                'string',
            ],
            'inventory' => ['sometimes', 'required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'last_synced_at' => ['nullable', 'date'],
        ];
    }
}
