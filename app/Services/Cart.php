<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session based shopping cart: [product_id => quantity].
 * Prices are never stored in the session — they are always re-read from the database.
 */
class Cart
{
    private const KEY = 'cart';

    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $cart = $this->raw();
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $quantity;
        $this->save($cart, [$product->id => $product]);
    }

    public function set(Product $product, int $quantity): void
    {
        $cart = $this->raw();
        if ($quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $quantity;
        }
        $this->save($cart, [$product->id => $product]);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        session([self::KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    /**
     * Resolves the cart into purchasable lines, silently dropping products that are
     * no longer active/available and clamping quantities to the current stock.
     *
     * @return Collection<int, array{product: Product, quantity: int, omr: float, toman: int}>
     */
    public function lines(): Collection
    {
        $raw = $this->raw();
        if (! $raw) {
            return collect();
        }

        $products = Product::query()->withCardData()->whereIn('id', array_keys($raw))->get()->keyBy('id');
        $clean = [];

        $lines = collect($raw)->map(function ($qty, $id) use ($products, &$clean) {
            $product = $products->get($id);
            if (! $product || ! $product->isPurchasable()) {
                return null;
            }
            $qty = max(1, min((int) $qty, $product->quantity));
            $clean[$id] = $qty;

            return [
                'product' => $product,
                'quantity' => $qty,
                'omr' => (float) $product->price_omr * $qty,
                'toman' => $product->tomanPrice() * $qty,
            ];
        })->filter()->values();

        session([self::KEY => $clean]);

        return $lines;
    }

    private function save(array $cart, array $products): void
    {
        foreach ($products as $id => $product) {
            if (isset($cart[$id])) {
                $cart[$id] = max(1, min($cart[$id], (int) $product->quantity));
            }
        }
        session([self::KEY => $cart]);
    }
}
