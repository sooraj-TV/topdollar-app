<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Helper\ResponseBuilder;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function postLogin(Request $request)
    {        
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {

            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                //return response()->json(['user_not_found'], 404);
                $data = array();
                return ResponseBuilder::result(404,"user_not_found",$data);                
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            // return response()->json(['token_expired'], 500);
            $data = array();
            return ResponseBuilder::result(500,"token_expired",$data);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            //return response()->json(['token_invalid'], 500);
            $data = array();
            return ResponseBuilder::result(500,"token_invalid",$data);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            //return response()->json(['token_absent' => $e->getMessage()], 500);
            $data = array();
            return ResponseBuilder::result(500,"token_absent",$data);

        }

        //return response()->json(compact('token'));
        $data = array('token' => $token);
        return ResponseBuilder::result(200,"success",$data);
    }
}