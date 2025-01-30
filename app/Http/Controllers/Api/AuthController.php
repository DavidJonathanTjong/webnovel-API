<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email'=> 'required|email|max:255|unique:users,email',
            'password'=> 'required',
            'confirm_password'=> 'required|same:password',
        ]); 

        if($validator ->fails()){
            return response()->json([
                'messege' => "All fields are mandetory",
                'error' => $validator -> errors(),
            ], 200);
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);
        if (!$user) {
            return response()->json([
                'message' => 'User registration failed'
            ], 500);
        }

        $success['token'] = $user->createToken('auth_token')->plainTextToken;
        $success['email'] = $user->email;
        $success['name'] = $user->name;
        
        return response()->json([
            'sukses' => true,
            'messege' => "Register Sukses",
            'data' => $success,
        ], 200);

    }

    public function login(Request $request){
        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json([
                'messege' => "Login Failed"
            ], Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make($request->all(),[
            'email'=> 'required',
            'password'=> 'required',
        ]); 

        if($validator ->fails()){
            return response()->json([
                'messege' => "All fields are mandetory",
                'error' => $validator -> errors(),
            ], 200);
        }

        // $auth = Auth::user();
        $success['token'] = $request->user()->createToken('auth_token')->plainTextToken;
        $success['email'] = $request->user()->email;
        $success['name'] = $request->user()->name;
        return response()->json([
            'messege' => "Login Sukses",
            'data' => $success
        ], 200);
    }
    
    function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success'=>true,
            'message'=>'Logout Sukses'
        ], 200);
    }

}