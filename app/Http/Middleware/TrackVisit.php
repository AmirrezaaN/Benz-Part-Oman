<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Lightweight, privacy-friendly visit counter for the stats page.
 * Stores only a daily salted hash of IP + user agent (no raw IPs).
 */
class TrackVisit
{
    private const BOT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|preview|monitor|curl|wget|python|headless/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && ! $request->ajax() && ! $request->expectsJson()
            && $response->getStatusCode() === 200
            && ! $request->is(config('shop.admin_path').'*')
            && ! preg_match(self::BOT_PATTERN, (string) $request->userAgent())) {
            try {
                $date = now()->toDateString();
                $hash = hash_hmac('sha256', $request->ip().'|'.$request->userAgent().'|'.$date, (string) config('app.key'));

                $updated = Visit::query()->where('date', $date)->where('visitor_hash', $hash)->update(['hits' => DB::raw('hits + 1')]);
                if (! $updated) {
                    Visit::query()->insertOrIgnore(['date' => $date, 'visitor_hash' => $hash, 'hits' => 1]);
                }
            } catch (Throwable) {
                // Never break a page because of statistics.
            }
        }

        return $response;
    }
}
