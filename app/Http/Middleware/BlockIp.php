<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();

        // Check if IP is blocked
        if (BlockedIp::isBlocked($ipAddress)) {
            // Return 403 Forbidden response
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'status' => 403,
                    'message' => translate('Your IP address has been blocked. Please contact administrator.'),
                ], 403);
            }

            // For web routes
            abort(403, translate('Your IP address has been blocked. Please contact administrator.'));
        }

        return $next($request);
    }
}