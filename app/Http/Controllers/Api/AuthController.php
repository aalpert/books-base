<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where("username", $request->username)->first();

//        dd($user);
        if ($user && Hash::check($request->password, $user->password) && $user->role == 'client') {
            return response($user->createToken('Books')->accessToken, 200);
        } else {
            return null;
        }
    }
}
