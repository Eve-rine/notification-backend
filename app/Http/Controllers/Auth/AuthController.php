<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // return request
        try{
            $validator=Validator::make($request->all(),[
                'name' =>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required',
                'phone_number'=>'required',
                'role'=>'required:string:in:user,admin'
            ]);
            if ($validator->fails()) {
                return response()
                    ->json([
                        'success'   =>false,
                        'message'   => $validator->errors()->first()
                    ],400);
            }
            $user=User::create([
                'name'  =>$request->input('name'),
                'email' =>$request->input('email'),
                'password'=>Hash::make($request->input('password')),
                'phone_number'=>$request->input('phone_number'),
                'role'  =>$request->input('role')
            ]);
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully created and account',
                    'data'      =>$user
                ]);
        }catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()],$exception->getCode());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $validator=Validator::make($request->all(),[
                'email'=>'required|email',
                'password'=>'required'
            ]);
            if ($validator->fails()) {
                return response()
                    ->json([
                        'success'   =>false,
                        'message'   => $validator->errors()->first()
                    ],400);
            }
            // check if user exists
            $user = User::where('email',$request->input('email'))->first();
            if (!$user){
                return response()
                    ->json(['message'=>'User not found'],404);
            }
            $credentials=[
                'email'     =>$request->input('email'),
                'password'  =>$request->input('password')
            ];
            if (!Auth::attempt($credentials)){
                return response()
                    ->json(['message'=>'You have entered incorrect credentials'],401);
            }
            return response()
                ->json([
                    'success'   =>true,
                    'message'   =>'You have successfully logged in',
                    'data'      => Auth::user(),
                    'token'     => Auth::user()->createToken('auth_token')->plainTextToken
                ],200);

        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()],$exception->getCode());
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $token = Auth::user()->token();
            $token->revoke();
            return response()
                ->json(['message'=>'You have been successfully logged out!'],200);
        } catch (Exception $exception) {
            return response()
                ->json(['message'=>$exception->getMessage()],$exception->getCode());
        }
    }
}
