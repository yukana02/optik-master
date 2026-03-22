<?php
namespace App\Http\Requests\Admin;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'items'                => 'required|array|min:1',
            'items.*.product_id'   => 'required|exists:products,id',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.diskon'       => 'nullable|numeric|min:0',
            'metode_bayar'         => 'required|in:tunai,transfer,qris,debit,kredit',
            'bayar'                => 'required|numeric|min:0',
            'diskon_persen'        => 'nullable|numeric|min:0|max:100',
            'diskon_nominal'       => 'nullable|numeric|min:0',
            'tipe_pasien'          => 'nullable|in:umum,bpjs',
            'no_bpjs'              => 'required_if:tipe_pasien,bpjs|nullable|string|max:30',
            'subsidi_bpjs'         => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'               => 'Keranjang belanja tidak boleh kosong.',
            'items.min'                    => 'Minimal satu produk harus dipilih.',
            'items.*.product_id.required'  => 'Produk tidak valid.',
            'items.*.qty.min'              => 'Jumlah minimal 1.',
            'metode_bayar.required'        => 'Metode pembayaran wajib dipilih.',
            'bayar.required'               => 'Jumlah bayar wajib diisi.',
            'no_bpjs.required_if'          => 'No. BPJS wajib diisi untuk pasien BPJS.',
        ];
    }
}
