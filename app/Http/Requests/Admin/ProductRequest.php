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
            'type'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'sometimes|boolean',
        ];
    }
}
