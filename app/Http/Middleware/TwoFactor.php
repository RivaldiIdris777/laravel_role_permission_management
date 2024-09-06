<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For otp feature
        $user = auth()->user();
        if(auth()->check() && $user->code) {
            if(!$request->is('verify')) {
                return redirect()->route('verify_otp.index');
            }
        }
        return $next($request);
    }
}
