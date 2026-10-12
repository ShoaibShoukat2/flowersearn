<?php

namespace App\Http\Middleware;

use Closure;

class MyMiddleware
{
    public function handle($request, Closure $next)
    {
      
        

        return $next($request);

        // If conditions are not met, you can return a response or redirect
        // Example: return response('Unauthorized', 401);
        // Example: return redirect()->route('login');
    }
}
