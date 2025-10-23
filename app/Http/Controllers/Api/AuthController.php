<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function login(Request $req)
    {
        $req->validate(['email' => 'required|email', 'password' => 'required']);
        $user = User::where('email', $req->email)->first();
        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }
        $token = $user->createToken('mobile')->plainTextToken;
        return ['token' => $token, 'user' => $user];
    }
}
