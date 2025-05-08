<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserLoggedIn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
          // Allow /welcome page without login
    if (!session()->has('user_id')) {
      if ($request->is('welcome')) {
          return $next($request);
      }

      // Redirect for all other protected routes
      return redirect('/')->with('error', 'Please log in to continue.');
  }

  $response = $next($request);

  return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                  ->header('Pragma', 'no-cache')
                  ->header('Expires', '0');
          
    }
}
