<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // pastikan user login
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'id' => ['nullable', 'integer', 'exists:products,id'],

            'shop_id' => ['required', 'integer', 'exists:shops,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],

            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],

            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],

            'status' => ['required', 'in:active,inactive'],

            'image' => ['nullable', 'string'],

            // 🔥 INI YANG KURANG
            'imageJson' => ['nullable', 'array'],
            'imageJson.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'shop_id.required' => 'Shop wajib dipilih',
            'shop_id.exists' => 'Shop tidak valid',

            'category_id.required' => 'Category wajib dipilih',
            'category_id.exists' => 'Category tidak valid',

            'name.required' => 'Nama product wajib diisi',
            'price.required' => 'Harga wajib diisi',
            'price.numeric' => 'Harga harus berupa angka',

            'stock.required' => 'Stock wajib diisi',
            'stock.integer' => 'Stock harus berupa angka',

            'status.required' => 'Status wajib dipilih',
            'status.in' => 'Status tidak valid',
        ];
    }

    protected function prepareForValidation(): void
    {
        // casting agar aman dari Flutter
        $this->merge([
            'price' => is_numeric($this->price) ? (float) $this->price : $this->price,
            'stock' => is_numeric($this->stock) ? (int) $this->stock : $this->stock,
        ]);
    }
}
