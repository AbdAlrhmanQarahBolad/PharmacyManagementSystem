<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\EmailValidation;
use App\Models\Location;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WarehouseDispenser;
use App\Models\WarehouseEmployee;
use Egulias\EmailValidator\EmailValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmail;
use App\Notifications\VerifyPharmacy;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection ;

class UserController extends Controller
{
    public function getContacts(User $u1)
    {
        $user = request()->user();
        if ($user->id != $u1->id) {
            return response()->json(['message' => 'unauthorized'], 400);
        }
        return ['contacts'=>User::whereHas('sentMessages', function($q) use ($user) {
            $q->where('to', $user->id);
        })
        ->orWhereHas('receivedMessages', function($q) use ($user) {
            $q->where('from', $user->id);
        })->get()] ;
        // $users = User::whereHas('sentMessages', function($q) use ($user) {
        //     $q->where('to', $user->id);
        // })
        // ->orWhereHas('receivedMessages', function($q) use ($user) {
        //     $q->where('from', $user->id);
        // })
        // ->with(['sentMessages', 'receivedMessages'])
        // ->orderByDesc(Message::selectRaw('MAX(created_at)')->whereColumn('user_id', 'users.id'))
        // ->get();
        // return User::whereHas('sentM')
    }
    public function getTokens(Request $request, $b, $c)
    {
        $request['grant_type'] = 'password';
        $request['client_id'] = env('CLIENT_ID');
        $request['client_secret'] = env('CLIENT_SECRET');
        $request['scope'] = '';
        $req = Request::create('/oauth/token/', 'POST', [
            'grant_type' => 'password',
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'username' => $b,
            'password' => $c,
            'scope' => '',
        ]);
        $response = Route::dispatch($req);
        return $response;
    }
    public function refresh(Request $request)
    {
        $request['grant_type'] = 'refresh_token';
        $request['client_id'] = env('CLIENT_ID');
        $request['client_secret'] = env('CLIENT_SECRET');
        $request['scope'] = '';
        $req = Request::create('/oauth/token/', 'POST', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_SECRET'),
            'scope' => '',
        ]);
        $response = Route::dispatch($req);
        return $response;
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|email',
            'password' => 'required'
        ]);
        return $this->getTokens($request, $request->username, $request->password);
    }
    private function resetPass(User $user)
    {
        // log out from all the user's devices (like when resetting password):
        if(!$user)
        $user = auth()->user();
        $tokens =  $user->tokens()->pluck('id');
        Token::whereIn('id', $tokens)
            ->update(['revoked' => true]);
        RefreshToken::whereIn('access_token_id', $tokens)->update(['revoked' => true]);
        return ['message' => 'logged out from all your devices!'];
    }
    public function logout()
    {
        //log out from this device only:
        $tokenId = auth()->user()->token()->id;
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);
        $tokenRepository->revokeAccessToken($tokenId);
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($tokenId);
        return response()->json(['message' => 'successfully logged out from this device'],200);
    }
    public function checkCode(Request $request, bool $del = false)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required',
        ]);
        $rec = EmailValidation::firstWhere('email', $request->email);
        $res = '';
        $st = 0;
        if (!$rec || !Hash::check($request->code, $rec->code)) {
            $res = ['message' => 'code is invalid'];
            $st = 400;
        } else {
            $res = ['message' => 'code is valid'];
            $st = 200;
        }
        if ($del) {
            if ($st === 400)
                return false;
            $rec->delete();
            return true;
        }
        return response()->json($res, $st);
    }
    public function forgotPass(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required',
            'password' => ['required', 'min:7', 'max:255'],
            'confirmPassword' => ['required', 'min:7', 'max:255', 'same:password'],
        ]);
        if (!$this->checkCode($request, true))
        return response()->json(['message'=>'the code is invalid'],400);
        $user = User::firstWhere('email', $request->email);
        $user->update(['password'=> $request->password]);
        $this->resetPass($user);
        return response()->json(['message'=>'password changed successfully!'],200);
    }
    //type of sendEmail: (forgot/register);
    public function sendEmail(Request $request)
    {
        $request->validate([
            'type' => ['required', Rule::in(['forgot', 'register'])],
        ]);
        if ($request->type == 'register') {
            $request->validate([
                'email' => 'required|email|max:255|unique:users,email',
            ]);
        } else {
            $request->validate([
                'email' => 'required',
            ]);
            $user  = User::firstWhere('email', $request->email);
            //maybe should edit the response (credentials!)..
            if (!$user)
                return response()->json(['message'=>'Email not found!'],422);
        }
        //generating string:
        $toBeSentString = '';
        for ($i = 0; $i < 6; $i++)
            $toBeSentString .= rand(0, 9);
        //creating a reference in the database:
        $newData = [
            'email' => $request->email,
            'code' => $toBeSentString
        ];
        $rec = EmailValidation::firstWhere('email', $request->email);
        if ($rec)
            $rec->update($newData);
        else
            EmailValidation::create($newData);
        return $toBeSentString;
        //send the toBeSentString as email:
        Notification::route('mail', $request->email)->notify(new VerifyEmail($toBeSentString));

        // end send email..
        //  return $toBeSentString;
        return response()->json(['message' => 'validation code has been sent successfully!'],200);
    }
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'min:7', 'max:255'],
            'code' => 'required',
            'area_id' =>'required|numeric|min:1|max:'. Area::max('id'),
            'location_desc'=>'required',
            'phoneNumber'=>'required',
            'gender'=>'required',
        ]);
        if (!$this->checkCode($request, true))
            return response()->json(['message'=>'the code is invalid'],400);
            $location = Location::create(request(['area_id','location_desc']));
        $request['location_id'] = $location->id;
        User::create(request(['name', 'email', 'password','location_id','phoneNumber','gender']));
        $request['username'] = $request->email;
        return $this->getTokens($request, $request->email, $request->password);
    }

}
