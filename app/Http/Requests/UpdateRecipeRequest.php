<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecipeRequest extends FormRequest
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
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price_of_dish' => 'sometimes|numeric|min:0',
            'price_to_buy' => 'sometimes|numeric|min:0',
            'calories' => 'sometimes|integer|min:0',
            'fats' => 'sometimes|numeric|min:0',
            'proteins' => 'sometimes|numeric|min:0',
            'carbohydrates' => 'sometimes|numeric|min:0',
            'ration' => 'sometimes|string|min:3|max:255',
            'type' => 'sometimes|string|min:3|max:255',
    
            'steps' => 'sometimes|array',
            'steps.*.title' => 'required_with:steps|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.order' => 'nullable|integer|min:0',
            'steps.*.duration' => 'nullable|string|max:255',
            'steps.*.ingredients' => 'nullable|array',
            'steps.*.ingredients.*.id' => 'required_with:steps.*.ingredients|integer|exists:ingredients,id',
            'steps.*.ingredients.*.amount' => 'nullable|numeric|min:0',
    
            'images' => 'sometimes|array',
            'images.*.path' => 'required_with:images|string',
            'images.*.is_main' => 'nullable|boolean',
            'images.*.position' => 'nullable|integer|min:0',
        ];
    }
}
