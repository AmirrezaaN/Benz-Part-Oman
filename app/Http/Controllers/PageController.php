<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact', ['brands' => Brand::query()->active()->ordered()->get(['id', 'name', 'name_fa'])]);
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function howToBuy(): View
    {
        return view('pages.how-to-buy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
