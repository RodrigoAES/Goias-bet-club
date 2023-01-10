<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {   
        if(Auth::user()->level != 'admin') {
            return response()->json([
                'status' => 'unauthorized',
                'error' => 'Somente a conta do admistrador geral do sistema tem permissão para executar está ação.'
            ]);
        }
        return $next($request);
    }
}
