<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Attendant;

class ValidateCardPermission
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
        if(Attendant::where('user_id', Auth::id())->validate_permission) {
            return $next($request);

        } else {
            return response()->json([
                'status' => 'unathorized',
                'error' => 'Unauthorized for this action.'
            ], 401);
        }
        
    }
}
