<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    public function register(Request $request){


        $request->validate([
                "name"=> "required|string",
                "subname"=> "required|string",
                "email"=> "required|string|email|unique:users",
                "role"=>"required|string",
                "password"=> "required|string|confirmed",
            
        ]);

        $user = User::create([
            "name"=> $request->name,
            "subname"=> $request->subname,
            "email"=> $request->email,
            "role"=> $request->role,
            "password"=> Hash::make($request->password),
        ]);

        $token = $user->createToken($request->name);

        

      return response()->json(['message'=>['user registered successfully'],'token'=>$token],200);

    }

    public function login(Request $request){

        $request->validate([
            'email'=> 'required|email',
            'password'=> 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user ||!Hash::check($request->password, $user->password)){

            return response()->json(['message'=> 'incorrect value'],401);


        }

        $token = $user->createToken($user->name);




        // if(auth()->attempt([$request->only('email','password')])){
        //     $user = auth()->user();
        //     $token = $user->createToken('Token Name')->accessToken;
    
        //     return response()->json(['token'=>$token],200);
        // }
    return [
        'user'=> $user,
        'token'=> $token->plainTextToken
        ];
    }
}
