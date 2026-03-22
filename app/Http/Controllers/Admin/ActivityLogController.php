<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('modul'))  { $query->where('modul', $request->modul); }
        if ($request->filled('user_id')) { $query->where('user_id', $request->user_id); }
        if ($request->filled('from'))   { $query->whereDate('created_at', '>=', $request->from); }
        if ($request->filled('to'))     { $query->whereDate('created_at', '<=', $request->to); }
        if ($request->filled('search')) {
            $query->where('aksi', 'like', '%'.$request->search.'%');
        }

        $logs   = $query->latest()->paginate(30)->withQueryString();
        $moduls = ActivityLog::distinct()->pluck('modul')->sort()->values();
        $users  = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('admin.activity-logs.index', compact('logs', 'moduls', 'users'));
    }
}
