<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createToken()
    {

        $tokenUser = User::where('email', 'token@gmail.com')->first();
        $token = $tokenUser->createToken($tokenUser->name);

        return $this->respondWithSuccess('Successfully created a token', [
            'token' => $token->plainTextToken
        ]);
    }

    // logout
    public function logout()
    {
        auth()->user()->tokens()->delete();
        return redirect()->route('login');
    }
}
