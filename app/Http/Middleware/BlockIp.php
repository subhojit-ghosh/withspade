<?php

namespace App\Http\Middleware;

use App\Models\BlockIp as ModelsBlockIp;
use Closure;
use Illuminate\Http\Request;

class BlockIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (ModelsBlockIp::where('ip', $request->ip())->first()) {
            abort(403);
        }
        return $next($request);
    }
}
