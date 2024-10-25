<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => [

                    'message' => 'Invalid credentials'
                ]
            ], 401));
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
        ]);
    }
}
