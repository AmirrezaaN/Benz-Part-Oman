<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('account.dashboard', [
            'user' => $user,
            'inquiries' => $user->inquiries()->latest('id')->take(5)->get(),
            'orders' => $user->orders()->latest('id')->take(5)->get(),
            'counts' => [
                'inquiries' => $user->inquiries()->count(),
                'answered' => $user->inquiries()->where('status', 'answered')->count(),
                'orders' => $user->orders()->count(),
                'open_orders' => $user->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count(),
            ],
        ]);
    }
}
