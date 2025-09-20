<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Notifications\ForgotPassword;
use App\Models\User;
use App\Models\UserOnlineLog;
use App\Mail\CommonMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|string',
            'user_type' => 'required|in:user,lawyer',
        ], [
            'email.required'     => __('messages.email_required'),
            'email.email'        => __('messages.valid_email'),
            'password.required'  => __('messages.password_required'),
            'user_type.required' => __('messages.user_type_required'),
            'user_type.in'       => __('messages.user_type_in'),
        ]);
        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $user = User::where('email', $request->email)
                    ->where('user_type', $request->user_type)
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => __('messages.invalid_credentials'), 'user' => null], 200);
        }

        if ($user->is_banned) {
            return response()->json([
                'status' => false,
                'message' => __('messages.account_disabled_deleted'),
            ], 200);
        }
        return $this->loginSuccess($user);
    }

    protected function loginSuccess($user)
    {
        $user->is_online = 1;
        $user->save();

        UserOnlineLog::create([
            'user_id' => $user->id,
            'status'  => 1
        ]);

        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => __('messages.login_success'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'image' => $user->image ? asset($user->image) : null,
                'phone' => $user->phone,
            ]
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email'     => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->where('user_type', 'user'),
                ],
            'phone'     => 'required|string|max:20',
            'password'  => 'required|string|min:6',
        ], [
            'full_name.required' => __('messages.full_name_required'),
            'email.required'     => __('messages.email_required'),
            'email.email'        => __('messages.valid_email'),
            'email.unique'       => __('messages.email_already_exist'),
            'phone.required'     => __('messages.phone_required'),
            'password.required'  => __('messages.password_required'),
            'password.min'       => __('messages.password_length'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $user = User::create([
            'name'     => $request->full_name,
            'user_type' => 'user',
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $array['subject'] = 'Registration Successful - Welcome to '.env('APP_NAME','Justyta').'!';
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = "Hi $user->name, <p> Congratulations and welcome to ".env('APP_NAME')."! We are delighted to inform you that your registration has been successfully completed. Thank you for choosing us as your trusted partner.</p>

            <p>We look forward to serving your legal needs.</p>
            <p>Thank you for choosing ".env('APP_NAME').". </p><hr>
            <p style='font-size: 12px; color: #777;'>
                This email was sent to $user->email. If you did not register on our platform, please ignore this message.
            </p>";
        Mail::to($user->email)->queue(new CommonMail($array));

        return response()->json([
            'status' => true,
            'message' => __('messages.success_register'),
            'data' => $user,
        ], 200);
    }

    public function forgetRequest(Request $request)
    {
        $email = $request->has('email') ? $request->email : '';
        if($email){
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.email_not_found')], 200);
            }else{
                $otp = rand(1000, 9999);
                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now()->addMinutes(10);
                $user->save();
                $user->notify(new ForgotPassword($user));
                return response()->json([
                    'status' => true,
                    'message' => __('messages.otp_send')
                ], 200);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => __('messages.email_required')], 200);
        }
    }

    public function verifyOTP(Request $request)
    {
        $code = $request->has('code') ? $request->code : '';
        $email = $request->has('email') ? $request->email : '';
        if($code){
            $user = User::where('email', $email)->where('otp', $code)->first();
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.invalid_code'),
                ], 200);
            }else{
                if (Carbon::parse($user->otp_expires_at)->isPast()) {
                    return response()->json([
                        'status' => false,
                        'message' => __('messages.otp_expired'),
                    ], 200);
                }

                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                return response()->json([
                    'status' => true,
                    'message' => __('messages.otp_verified_successfully'),
                ], 200);
            }
        }else{
            return response()->json([
                'status' => false,
                'message' => __('messages.otp_required')], 200);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ], [
            'email.required'     => __('messages.email_required'),
            'email.email'        => __('messages.valid_email'),
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_length')
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $user = User::where('user_type', $request->user_type)->where('email', $request->email)->first();
        if($user){
            $user->password = Hash::make($request->password);
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => __('messages.password_reset_success'),
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => __('messages.user_not_found'),
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();
            $user->is_online = 1;
            $user->save();

            UserOnlineLog::create([
                'user_id' => $user->id,
                'status'  => 0
            ]);
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => __('messages.logout_success'),
            ],200);
        }

        return response()->json([
            'status' => false,
            'message' => __('messages.user_not_found'),
        ], 200);
    }
}
