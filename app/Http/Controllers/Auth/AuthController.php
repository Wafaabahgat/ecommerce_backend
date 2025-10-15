<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\Auth\RegistrationResource;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use App\Notifications\LoginNotification;
use App\Notifications\ResetPasswordNotification;
use App\Traits\UploadImageTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use UploadImageTrait;
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }
    public function register(RegisterRequest $request)
    {
        $newUser = $request->all();

        $newUser['password'] = Hash::make($request->post('password'));
        // dd($newUser);
        $user = User::create($newUser);
        $user->notify(new EmailVerificationNotification());
        $us = $user->refresh();

        return Helper::sendSuccess('please check your email to verify your email.', new RegistrationResource($us), 201);
    }

    public function login(LoginRequest $request)
    {
        $cred = [
            'email' => $request->post('email'),
            'password' => $request->post('password'),
        ];

        if (auth()->attempt($cred)) {
            $user = Auth::user();
            $user->tokens()->delete();
            // $user->notify(new LoginNotification());
            return Helper::sendSuccess('Login Successfully', new RegistrationResource($user), 200);
        }

        return Helper::sendError('email or password is wrong', [], 401);
    }

    public function emailVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|max:6',
        ]);

        $user = User::where('email', $request->post('email'))->first();
        if (!$user) {
            return Helper::sendError('There is no user with this email.', [], 404);
        }

        $v_otp = $this->otp->validate($request->post('email'), $request->post('otp'));

        if (!$v_otp->status) {
            return Helper::sendError('Code is invalid....', [], 404);
        }

        $user->update([
            'email_verified_at' => now()
        ]);

        return Helper::sendSuccess('Email verified successfully.', [], 200);
    }

    public function sendEmailVerify(Request $request)
    {

        $request->user()->notify(new EmailVerificationNotification());

        return Helper::sendSuccess('Email verification code was sent, check your email.', [], 200);
    }

    public function forgetPassword(Request $request)
    {
        // $request->validate([
        //     'email' => 'required|email|exists:users,email',
        // ]);

        $user = User::where('email', $request->post('email'))->first();
        if (!$user) {
            return Helper::sendError('There is no user with this email.', [], 404);
        }

        $user->notify(new ResetPasswordNotification());

        return Helper::sendSuccess('Forgot Password code was sent, check your email.', [], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|max:6',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('email', $request->post('email'))->first();
        if (!$user) {
            return Helper::sendError('There is no user with this email.', [], 404);
        }

        $v_otp = $this->otp->validate($request->post('email'), $request->post('otp'));

        if (!$v_otp->status) {
            return Helper::sendError('Code is invalid....', [], 404);
        }

        $user->update([
            'password' => Hash::make($request->post('password'))
        ]);
        $user->tokens()->delete();

        return Helper::sendSuccess('Password Resetted successfully.', [], 200);
    }

    public function profile(Request $request)
    {
        $user = $request->user();

        return Helper::sendSuccess('success', new UserResource($user), 200);

    }

    public function profileUpdate(Request $request)
    {
        $user = $request->user();
        $newUser = $request->all();

        $newUser['image'] = $this->uploadImg($request, 'users') ?? $user->image;
        $user->update($newUser);
        $us = $user->refresh();

        return Helper::sendSuccess('profile updated successfully.', new UserResource($us), 200);
    }
}
