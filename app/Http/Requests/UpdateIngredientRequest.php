<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:1'],
            'currency' => ['sometimes', 'string', 'max:3'],
            'unit' => ['sometimes', 'string', 'max:32'],
            'category' => ['sometimes', 'string', 'max:64'],
        ];
    }
}