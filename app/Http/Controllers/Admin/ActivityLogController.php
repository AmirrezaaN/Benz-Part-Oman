<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.activity', [
            'logs' => ActivityLog::query()->with('user:id,username,first_name,last_name,internal_code')
                ->when($request->query('action'), fn ($q, $v) => $q->where('action', 'like', $v.'%'))
                ->latest('id')->paginate(40)->withQueryString(),
        ]);
    }
}
