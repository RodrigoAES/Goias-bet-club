<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrSubAdmin
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
        if(Auth::user()->level === 'admin' || Auth::user()->level === 'sub-admin') {
            return $next($request);

        } else {
            return response()->json([
                'status' => 'unauthorized',
                'error' => 'Está ação não é autorizada ao nivel desta conta.'
            ]);
        }
        
    }
}
