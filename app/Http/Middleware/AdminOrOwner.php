<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrOwner
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
        if(Auth::user()->level === 'admin' || Auth::user()->id == $request->route('parameter_id')) {
            return $next($request);

        } else {
            return response()->json([
                'status' => 'unauthorized',
                'error' => 'Ação não autorizada para está conta.'
            ]);
        }
        
    }
}
