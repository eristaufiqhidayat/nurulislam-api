<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Sesuaikan jika pakai policy
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_id'     => ['required', 'integer', 'exists:shops,id'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price'       => ['required', 'numeric', 'min:0'],
            'stock'       => ['required', 'integer', 'min:0'],
            'status'      => ['required', 'in:active,inactive'],
            'image'       => ['nullable', 'string', 'max:255'], // 🔥 filename saja
        ];
    }

    public function messages(): array
    {
        return [
            'shop_id.required'     => 'Shop wajib dipilih',
            'shop_id.exists'       => 'Shop tidak valid',

            'category_id.required' => 'Kategori wajib dipilih',
            'category_id.exists'   => 'Kategori tidak valid',

            'name.required'        => 'Nama produk wajib diisi',
            'name.max'             => 'Nama produk maksimal 255 karakter',

            'price.required'       => 'Harga wajib diisi',
            'price.numeric'        => 'Harga harus berupa angka',
            'price.min'            => 'Harga tidak boleh minus',

            'stock.required'       => 'Stock wajib diisi',
            'stock.integer'        => 'Stock harus berupa angka',
            'stock.min'            => 'Stock tidak boleh minus',

            'status.required'      => 'Status wajib dipilih',
            'status.in'            => 'Status tidak valid',

            'image.string'         => 'Image harus berupa nama file',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Optional: trim string
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
        ]);
    }
}
