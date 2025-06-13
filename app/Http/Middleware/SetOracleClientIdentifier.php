<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetOracleClientIdentifier
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $username = Auth::user()->username ?? Auth::user()->email ?? Auth::id();

            try {
                DB::statement("BEGIN DBMS_SESSION.SET_IDENTIFIER(:user); END;", [
                    'user' => $username
                ]);
            } catch (\Exception $e) {
                
                 Log::warning("Failed to set Oracle client identifier: " . $e->getMessage());
            }
        }

        return $next($request);
    }
}
