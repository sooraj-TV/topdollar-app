<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Helper\ResponseBuilder;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Model\Api;

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
        $input = $request->all();
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
        //echo $this->jwt->user()->id; exit;
        $data = array(
            'token'     => $token,
            'user_id'   => $this->jwt->user()->id
        );
        $update_data = array(
            'device_id' => $input['device_id'],
            'device_token' => $input['device_token'],
            'user_id'   => $this->jwt->user()->id
        );
        Api::updateDeviceDetails($update_data);
        return ResponseBuilder::result(200,"success",$data);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        //echo Hash::make($request->post('password')); exit;
        if($validator->fails()){
                //return response()->json($validator->errors()->toJson(), 400);
                return ResponseBuilder::result(400, $validator->errors());
        }
        
        $user = User::create([
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'password' => Hash::make($request->post('password')),
        ]);

        $token = $this->jwt->fromUser($user);

        return response()->json(compact('user','token'),201);
    }
}