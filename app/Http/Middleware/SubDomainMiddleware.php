<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Support\Facades\URL;

class SubDomainMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Customer::current()) {
            return $next($request);
        } else {
            abort(404);
        }
    }
}
