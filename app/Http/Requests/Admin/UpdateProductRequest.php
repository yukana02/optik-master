<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id'  => 'required|exists:categories,id',
            'nama'         => 'required|string|max:150',
            'deskripsi'    => 'nullable|string',
            'merek'        => 'nullable|string|max:100',
            'harga_beli'   => 'required|numeric|min:0',
            'harga_jual'   => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'satuan'       => 'required|string|max:20',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'    => 'nullable|boolean',
            'hapus_gambar' => 'nullable|boolean',
        ];
    }
}
