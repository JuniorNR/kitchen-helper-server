<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Enums\ApiCode;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['string', 'max:255'],
            'email' => ['required', 'email', 'unique:users', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $expiresAt = now()->addDays(1);
		$token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
            'code' => ApiCode::USER_REGISTERED->value
        ], 201);
    }
	public function login(Request $request)
	{
		$validated = $request->validate([
			'email' => ['required', 'email'],
			'password' => ['required', 'string'],
		]);

		$user = User::where('email', $validated['email'])->first();

		if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'token' => null,
                'user' => null,
                'code' => ApiCode::INVALID_CREDENTIALS->value
            ], 404);
		}


		$expiresAt = now()->addDays(1);
		$token = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

		return response()->json([
            'data' => [
                'token' => $token,
                'user' => $user,
            ],
            'code' => ApiCode::USER_LOGGED_IN->value
        ], 200);
	}

	public function authUser()
	{
        $user = Auth::user();
		return response()->json([
            'data' => [
                'user' => $user,
            ],
            'code' => ApiCode::USER_RETURNED->value
        ], 200);
	}

	public function logout()
	{
		// Отозвать текущий токен
		Auth::user()->currentAccessToken()?->delete();
		return response()->json([
            'data' => [
                'user' => null,
            ],
            'code' => ApiCode::USER_LOGGED_OUT->value
        ], 200);
	}

	public function logoutAll(Request $request)
	{
		Auth::user()->tokens()->delete();
		return response()->json([
            'data' => [
                'user' => null,
            ],
            'code' => ApiCode::USER_LOGOUT_ALL->value
        ], 200);
	}
}