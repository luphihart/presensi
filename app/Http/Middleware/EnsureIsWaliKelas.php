<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;

class EnsureIsWaliKelas
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || $request->user()->role !== UserRole::WaliKelas) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
