<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckIsAdmin
{
       /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
   
        public function handle(Request $request, Closure $next): Response
        {

            if(!auth()->check()){
                return $next($request);
            }

            if (auth()->user()->is_admin) {
               return $next($request);
            }
            return abort(401, 'Unauthorized.');
        }
}
