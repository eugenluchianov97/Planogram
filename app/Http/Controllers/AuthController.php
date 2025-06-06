<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login(LoginRequest $loginRequest) {

        if (
            Auth::guard()->attempt($loginRequest->validated())
        ) {
            return redirect('/dashboard');
        }

        return response()->json([
            'errors' => [
                'login' => [
                    'Логин или пароль введены неверно'
                ]
            ]
        ], 403);

    }

    // public function logout() {
    //     Auth::logout();
    //     return Inertia::location('/');
    // }
}
