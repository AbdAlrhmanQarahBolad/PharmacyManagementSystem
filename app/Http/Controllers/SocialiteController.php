<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;

class SocialiteController extends Controller
{
    public function check($access_token)
    {
        try {
            Socialite::driver('google')->userFromToken($access_token);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
    public function handleGoogleCallback(Request $request)
    {
        $validator = Validator::make($request->only('google_access_token'), [
            'google_access_token' => ['required', 'string'],
        ]);
        if ($validator->fails())
            return response()->json($validator->errors(), 422);

        //geting user from access_google_token and check it
        if ($this->check($request->google_access_token)) {
            $googleUser = Socialite::driver('google')->userFromToken($request->google_access_token);
        } else {
            return response()->json(['message' => 'unauthorized'], 400);
        }
        //end
        $user = User::firstOrCreate([
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
            ]);
        $request['username'] = $user->email;
        $request['password'] = '*';
        return app(UserController::class)->getTokens($request, $request->email, $request->password);
    }
}
