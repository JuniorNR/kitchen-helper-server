<?php

namespace App\Http\Controllers;

use App\Enums\ApiCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
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

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['sometimes', 'string', 'min:8', 'confirmed'],
        ]);

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }
        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }
        if (array_key_exists('password', $validated)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return response()->json([
            'data' => [
                'user' => $user,
            ],
            'code' => ApiCode::USER_UPDATED->value
        ], 200);
    }
}


