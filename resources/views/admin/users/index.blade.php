@extends('layouts.admin')
@section('title','Manajemen User')
@section('page-title','Manajemen User')
@section('content')
<div class="card">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <div><i class="bi bi-person-gear text-primary me-2"></i>Daftar User</div>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah User
        </a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Nama</th>
                    <th class="d-none d-md-table-cell">Email</th>
                    <th>Role</th>
                    <th class="d-none d-lg-table-cell">Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td class="ps-3">
                        <div class="fw-semibold">
                            {{ $u->name }}
                            @if($u->id === auth()->id())
                            <span class="badge bg-success ms-1" style="font-size:.65rem">Anda</span>
                            @endif
                        </div>
                        <small class="text-muted d-md-none">{{ $u->email }}</small>
                    </td>
                    <td class="d-none d-md-table-cell text-muted">{{ $u->email }}</td>
                    <td>
                        @foreach($u->roles as $role)
                        <span class="badge {{ $role->name=='super_admin' ? 'bg-danger' : ($role->name=='admin' ? 'bg-primary' : 'bg-secondary') }}"
                              style="font-size:.7rem">
                            {{ ucfirst(str_replace('_',' ',$role->name)) }}
                        </span>
                        @endforeach
                    </td>
                    <td class="d-none d-lg-table-cell text-muted small">{{ $u->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('users.edit',$u) }}" class="btn btn-xs btn-outline-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy',$u) }}"
                                  data-confirm="Hapus user {{ $u->name }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-xs btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada user</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="card-footer">{{ $users->links() }}</div>
    @endif
</div>
@endsection
