@extends('layouts.admin')
@section('title', 'Edit Supplier')
@section('page-title', 'Edit Supplier')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header p-3"><i class="bi bi-truck text-warning me-2"></i>Edit Supplier — {{ $supplier->kode_supplier }}</div>
    <div class="card-body p-4">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Kode Supplier</label>
                <input type="text" class="form-control" value="{{ $supplier->kode_supplier }}" disabled>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                       value="{{ old('nama', $supplier->nama) }}" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Kontak Person</label>
                <input type="text" name="kontak_person" class="form-control" value="{{ old('kontak_person', $supplier->kontak_person) }}">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $supplier->telepon) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email) }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $supplier->alamat) }}</textarea>
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                           {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive">Supplier Aktif</label>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Perubahan</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
    </div>
</div>
</div>
</div>
@endsection
