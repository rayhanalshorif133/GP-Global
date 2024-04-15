<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return $this->respondWithError('Invalid credentials');
        }

        $token = auth()->user()->createToken(auth()->user()->name);

        return $this->respondWithSuccess('Successfully logged in', [
            'token' => $token->plainTextToken
        ]);
    }
}
