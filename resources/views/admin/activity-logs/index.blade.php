@extends('layouts.admin')
@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('content')
<div class="card">
    <div class="card-header p-3">
        <form class="d-flex flex-wrap gap-2" method="GET">
            <select name="modul" class="form-select form-select-sm" style="width:130px">
                <option value="">Semua Modul</option>
                @foreach($moduls as $m)
                <option value="{{ $m }}" {{ request('modul')==$m?'selected':'' }}>{{ ucfirst(str_replace('_',' ',$m)) }}</option>
                @endforeach
            </select>
            <select name="user_id" class="form-select form-select-sm" style="width:160px">
                <option value="">Semua User</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
                @endforeach
            </select>
            <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}" style="width:140px">
            <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}" style="width:140px">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari aksi..." value="{{ request('search') }}" style="width:180px">
            <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-filter"></i></button>
            @if(request()->anyFilled(['modul','user_id','from','to','search']))
            <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-light">Reset</a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Waktu</th><th>User</th><th>Modul</th><th>Aksi</th><th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="ps-3 text-muted small text-nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td class="small">
                        <span class="fw-semibold">{{ $log->user->name ?? '<i>Deleted</i>' }}</span>
                        @if($log->user)
                        <br><span class="text-muted" style="font-size:.7rem">{{ $log->user->getRoleNames()->first() }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-light text-dark border text-capitalize" style="font-size:.7rem">{{ str_replace('_',' ',$log->modul) }}</span></td>
                    <td class="small">{{ $log->aksi }}</td>
                    <td class="text-muted small">{{ $log->ip_address }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada log aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer py-2 px-3">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
