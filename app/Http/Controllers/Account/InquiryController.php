<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        return view('account.inquiries.index', [
            'inquiries' => $request->user()->inquiries()->with('product:id,name,slug')->latest('id')->paginate(10),
        ]);
    }

    public function show(Request $request, Inquiry $inquiry): View
    {
        abort_unless($inquiry->user_id === $request->user()->id, 404);

        return view('account.inquiries.show', ['inquiry' => $inquiry->load('product', 'brand')]);
    }
}
