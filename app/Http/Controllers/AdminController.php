<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    use ApiResponser;

    public function login(Request $request)
    {
        $request->validate(['user_name' => 'required', 'password' => 'required']);
        $user = User::where([['user_name', $request->user_name], ['role', 'admin']])->first();
        // return $user;
        if ($user) {
            if ($user->is_verified && $user->role == 'admin') {
                if (Hash::check($request->password, $user->password)) {
                    $user->tokens()->delete();
                    return $this->success(null, [
                        'user' => $user,
                        'role' => $user->role ? $user->role : null,
                        'token' => $user->createToken('API-Token')->accessToken,
                        'token_type' => 'Bearer',
                    ]);
                }
                return $this->error('Credentials not match', null, 422);
            } else {
                return $this->error('Account not verified', null, 403);
            }
        } else {
            return $this->error('No User Found', null, 404);
        }
    } //login

    public function logout()
    {
        Auth::user()->tokens()->delete();
        return $this->success('Tokens Revoked');
    } // logout

    public function sendMail(Request $request)
    {
        $request->validate(['email' => 'required']);

        $to_email = $request->email;
        $encodedEmail =  urlencode($request->email);
        $link = url('/') . '/user/register/' . $encodedEmail;
        // $link = app()->urldecode();
        // $link = app_path() . $request->email;
        // $link = urlencode($link);
        $data = array('name' => "Hello New User", 'email' => $request->email, 'body' => "Welcome TO application you can register your self by clicking link below", 'link' => $link);
        Mail::send('mail', $data, function ($message) use ($to_email) {
            $message->to($to_email)
                ->subject('Register Your Self');
            $message->from('jhonnydeep1122@gmail.com', 'Test Mail');
        });
        return $this->success('Email Sent');
    }
}
