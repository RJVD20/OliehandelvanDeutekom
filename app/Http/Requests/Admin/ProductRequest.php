<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand'       => 'nullable|string|max:100',
            'model_type'  => 'nullable|string|max:100',
            'used'        => 'sometimes|boolean',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'specifications' => 'nullable|array|max:50',
            'specifications.*.name' => 'required|string|max:100',
            'specifications.*.value' => 'required|string|max:255',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'sometimes|boolean',
            'featured'     => 'sometimes|boolean',
            'remove_image' => 'sometimes|boolean',
            'slug'         => 'nullable|string|max:255',
            'tier_pricing_enabled' => 'sometimes|boolean',
            'delivery_tiers' => 'nullable|array|max:20',
            'delivery_tiers.*.quantity' => 'required|integer|min:1|max:999',
            'delivery_tiers.*.price' => 'required|numeric|min:0|max:9999.99',
            'pickup_tiers' => 'nullable|array|max:20',
            'pickup_tiers.*.quantity' => 'required|integer|min:1|max:999',
            'pickup_tiers.*.price' => 'required|numeric|min:0|max:9999.99',
        ];
    }
}
