<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class BasicAuth
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
        // get request data
        $username = $request->getUser();
        $password = $request->getPassword();
        // check if username and password are correct
        if ($username == 'b2m.gpglobal' && $password == 'HE1]F+Hcf<37;2') {
            return $next($request);
        }else{
            return response()->json([
                'status'   => false,
                'errors'  => true,
                'message'  => 'Unauthenticated user request',
                'code'    => 401
         ], 401);
        }

    }
}
