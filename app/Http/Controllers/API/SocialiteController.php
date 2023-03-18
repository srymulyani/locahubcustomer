<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function __invoke(Request $request, $driver)
    {
        // Getting the user from socialite using token from google
        $user = Socialite::driver($driver)->stateless()->userFromToken($request->token);

        // Getting or creating user from db
        $userFromDb = User::firstOrCreate(
            [
                'email' => $user->getEmail(),
                'provider_id' => $user->getId(),
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->offsetGet('given_name'),
                'avatar' => $user->getAvatar(),
                'provider_by' => $driver,
                'roles' => 'USER'
            ]
        );

        $token = $userFromDb->createToken('authToken')->plainTextToken;

        // Returning response
        return ResponseFormatter::success([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $userFromDb,
        ],  'Success', 200);
    }
}
