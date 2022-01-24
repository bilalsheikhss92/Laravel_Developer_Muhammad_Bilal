<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//admin
Route::post('/admin/login', [AdminController::class, 'login']);

Route::prefix('/admin')->middleware('auth:api')->group(function () {
    Route::post('/logout', [AdminController::class, 'logout']);
    Route::post('/send/mail', [AdminController::class, 'sendMail']);
});

Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/verify', [UserController::class, 'verify']);

Route::prefix('/user')->middleware('auth:api')->group(function () {
    Route::post('/change/password', [UserController::class, 'changePassword']);
    Route::post('/profile', [UserController::class, 'profile']);
    Route::post('/update/profile', [UserController::class, 'updateProfile']);
    Route::post('/logout', [UserController::class, 'logout']);
});

//user







Route::get('/test', function () {
    // $request = "";
    $email = "17237009@gift.edu.pk";
    $to_email = $email;
    $link = url('/') . '/user/register/' . $email;
    // $link = app()->urldecode();
    // $link = app_path() . $email;
    // $link = urlencode($link);
    $data = array('name' => "Hello New User", 'email' => $email, 'body' => "Welcome TO application you can register your self by clicking link below", 'link' => $link);
    Mail::send('mail', $data, function ($message) use ($to_email) {
        $message->to($to_email)
            ->subject('Register Your Self');
        $message->from('jhonnydeep1122@gmail.com', 'Test Mail');
    });

    // return $link;
});
