@extends('layouts.admin')
@section('title', 'Tambah Supplier')
@section('page-title', 'Tambah Supplier')
@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header p-3"><i class="bi bi-truck text-primary me-2"></i>Form Tambah Supplier</div>
    <div class="card-body p-4">
    <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Nama Supplier <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                       value="{{ old('nama') }}" placeholder="PT. Contoh Supplier" required>
                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Kontak Person</label>
                <input type="text" name="kontak_person" class="form-control" value="{{ old('kontak_person') }}" placeholder="Nama PIC">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Telepon</label>
                <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" placeholder="08xx-xxxx-xxxx">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="supplier@email.com">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap supplier">{{ old('alamat') }}</textarea>
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                           {{ old('is_active', '1') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="isActive">Supplier Aktif</label>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan</button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-light">Batal</a>
        </div>
    </form>
    </div>
</div>
</div>
</div>
@endsection
