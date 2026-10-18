<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Inquiry;
use App\Support\Identifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = Inquiry::query()->with(['user:id,first_name,last_name,username,internal_code', 'product:id,name,slug'])
            ->when($request->query('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->query('q'), function ($q, $v) {
                $q->where(fn ($q) => $q->where('code', 'like', "%{$v}%")->orWhere('part_name', 'like', "%{$v}%")
                    ->orWhere('vin', 'like', "%{$v}%")->orWhere('part_number', 'like', "%{$v}%"));
            })
            ->latest('id')->paginate(20)->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'counts' => Inquiry::query()->selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        return view('admin.inquiries.show', ['inquiry' => $inquiry->load('user', 'product', 'brand', 'repliedBy')]);
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $numeric = fn ($v) => $v === null || $v === '' ? null : str_replace([',', '،', ' '], '', Identifier::normalizeDigits((string) $v));
        $request->merge([
            'quoted_price_omr' => $numeric($request->input('quoted_price_omr')),
            'quoted_price_toman' => $numeric($request->input('quoted_price_toman')),
        ]);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(config('shop.inquiry_statuses')))],
            'admin_reply' => ['nullable', 'string', 'max:5000'],
            'quoted_price_omr' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'quoted_price_toman' => ['nullable', 'integer', 'min:0', 'max:999999999999'],
        ], [], ['status' => 'وضعیت', 'admin_reply' => 'پاسخ', 'quoted_price_omr' => 'قیمت پیشنهادی (OMR)', 'quoted_price_toman' => 'قیمت پیشنهادی (تومان)']);

        $replyChanged = ($data['admin_reply'] ?? null) !== $inquiry->admin_reply;

        $inquiry->forceFill($data);
        if ($replyChanged && filled($data['admin_reply'])) {
            $inquiry->replied_by = $request->user()->id;
            $inquiry->replied_at = now();
        }
        $inquiry->save();

        ActivityLog::record('inquiry.updated', $inquiry, 'به‌روزرسانی استعلام '.$inquiry->code.' ('.$inquiry->statusLabel().')');

        return back()->with('success', 'استعلام به‌روزرسانی شد.');
    }
}
