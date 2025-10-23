<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'nullable|string',
            'price_of_dish' => 'required|numeric|min:0',
            'price_to_buy' => 'required|numeric|min:0',
            'calories' => 'required|integer|min:0',
            'fats' => 'required|numeric|min:0',
            'proteins' => 'required|numeric|min:0',
            'carbohydrates' => 'required|numeric|min:0',
            'ration' => 'required|string|min:3|max:255',
            'type' => 'required|string|min:3|max:255',
            'duration' => 'required|integer|min:0',
    
            'steps' => 'nullable|array',
            'steps.*.title' => 'required_with:steps|string',
            'steps.*.description' => 'nullable|string',
            'steps.*.order' => 'nullable|integer|min:0',
            'steps.*.duration' => 'nullable|integer|min:0',
            'steps.*.ingredients' => 'nullable|array',
            'steps.*.ingredients.*.id' => 'required_with:steps.*.ingredients|integer|exists:ingredients,id',
            'steps.*.ingredients.*.amount' => 'nullable|numeric|min:0',
    
            'images' => 'nullable|array',
            'images.*.file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'images.*.is_main' => 'nullable|boolean',
            'images.*.position' => 'nullable|integer|min:0',
        ];
    }
}
