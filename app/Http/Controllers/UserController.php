<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Mockery\Generator\StringManipulation\Pass\Pass;

class UserController extends Controller
{
    use ApiResponser;
    //
    public function login(Request $request)
    {
        $request->validate(['user_name' => 'required', 'password' => 'required']);
        $user = User::where([['user_name', $request->user_name], ['role', 'user']])->first();
        // return $user;
        if ($user) {
            if ($user->is_verified && $user->role == 'user') {
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

    public function registerView(Request $request, string $mail)
    {
        $email = urldecode($mail);
        $user = User::where('email', $email)->first();
        if ($user) {
            return abort('403');
        }
        return view('register', compact('email'));
    } // registerView

    public function register(Request $request)
    {
        // return $request;
        $request->validate([
            'user_name' => 'required|min:4|max:20|unique:users,user_name',
            'password' => 'required|min:6',
            'email' => 'required|unique:users,email',
        ]);
        // $email = $mail;
        $random = rand(100000, 999999);
        $user_name = str_replace(' ', '', $request->user_name);
        $user = User::create([
            'name' => $request->user_name,
            'user_name' => $user_name,
            'password' => bcrypt($request->password),
            'email' => $request->email,
            'verification_code' => $random,
            'role' => 'user'
        ]);
        $to_email = $request->email;
        $data = array('name' => "Hello New User", 'email' => $request->email, 'body' => "Here Is your account verification code", 'verification_code' => $random);
        Mail::send('password', $data, function ($message) use ($to_email) {
            $message->to($to_email)
                ->subject('Verify Your Account');
            $message->from('jhonnydeep1122@gmail.com', 'Test Mail');
        });
        return $this->success('User Registered');
    } //register

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if ($user->verification_code == $request->code) {
                $user->update([
                    'is_verified' => 1
                ]);
                return $this->success("User Profile Verified");
            } else {
                return $this->error("Invalid Email or Code");
            }
        } else {
            return $this->error("User not found");
        }
    } //verify

    public function updateProfile(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            if ($request->has('image')) {
                $data = getimagesize($request->image);
                $width = $data[0];
                $height = $data[1];
                $image = NULL;
                if ($width == 256 && $height == 256) {
                    $file = $request->file('image');
                    $filename = str_replace(' ', '', 'image' . time() . '.' . $file->getClientOriginalName());
                    $location = app()->basePath('/public/user/');
                    $file->move($location, $filename);
                    $image = "/public/user/" . $filename;
                }
            }
            if ($request->user_name) {
                $request->validate([
                    'user_name' => 'required|min:4|max:20|unique:users,user_name,' . $user->id,
                ]);
                $user_name = $request->user_name;
            }
            $user->update([
                'name' => $request->name ?  $request->name : $user->name,
                'avatar' => $image ?  $image : $user->avatar,
                'user_name' => $user_name ?  $user_name : $user->user_name,
            ]);

            return $this->success('user profile updated');
        }
    }  //updateProfile

    public function changePassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'current_password' => 'required|min:6|max:255',
            'password' => 'required|min:6|max:255',
            'password_confirm' => 'required|same:password',
        ]);
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->tokens()->delete();
                $user->update([
                    'password' => bcrypt($request->password),
                ]);
                return $this->success('user password updated');
            }
            return $this->error('Incorrect Password');
        }
        return $this->error('Invalid User');
    } //changePassword

    public function profile()
    {
        $user_id = Auth::id();
        $user = User::where('id', $user_id)->first();
        return $this->success('user profilr', $user);
    } // profile
}
