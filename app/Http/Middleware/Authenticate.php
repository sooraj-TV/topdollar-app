<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Http\Helper\ResponseBuilder;
use Tymon\JWTAuth\JWTAuth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth, JWTAuth $jwt)
    {
        $this->auth = $auth;
        $this->jwt = $jwt;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {     
        
        //$user = $this->jwt->User();
        $user = $this->jwt->user();
        //dd($user);
        echo $this->jwt->user()->id;
        
        if ($this->auth->guard($guard)->guest()) {
            //return response('Unauthorized.', 401);
            return ResponseBuilder::result(401,'unauthorized_access');
        }

        return $next($request);
    }
}
