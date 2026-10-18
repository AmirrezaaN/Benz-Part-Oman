<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends ProductController
{
    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    /** Live suggestions for the header search box. */
    public function suggest(Request $request): JsonResponse
    {
        $term = mb_substr(trim((string) $request->query('q')), 0, 100);

        if (mb_strlen($term) < 2) {
            return response()->json(['items' => []]);
        }

        $items = Product::query()->active()->withCardData()->search($term)->latest('id')->take(6)->get()
            ->map(fn (Product $p) => [
                'name' => $p->name,
                'part_number' => $p->part_number,
                'chassis' => $p->chassisCodes(),
                'image' => $p->imageUrl(),
                'url' => route('products.show', $p),
                'price_toman' => $p->tomanPrice() ? format_toman($p->tomanPrice()) : null,
                'price_omr' => $p->price_omr !== null ? format_omr($p->price_omr) : null,
            ]);

        return response()->json([
            'items' => $items,
            'all_url' => route('search', ['q' => $term]),
        ]);
    }
}
