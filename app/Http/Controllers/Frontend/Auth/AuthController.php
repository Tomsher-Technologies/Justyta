<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Notifications\ForgotPassword;
use App\Models\User;
use App\Mail\CommonMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|string',
        ], [
            'email.required'     => __('messages.email_required'),
            'email.email'        => __('messages.valid_email'),
            'password.required'  => __('messages.password_required'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)
            ->whereIn('user_type', ['lawyer', 'vendor', 'translator', 'user'])
            ->first();

        if (!$user || !\Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => __('messages.invalid_credentials')]);
        }

        Auth::guard('frontend')->login($user);

        return match ($user->user_type) {
            'lawyer' => redirect()->route('lawyer.dashboard'),
            'vendor' => redirect()->route('vendor.dashboard'),
            'translator' => redirect()->route('translator.dashboard'),
            'user' => redirect()->route('user.dashboard'),
            default => redirect()->route('frontend.login')->withErrors(['error' => 'Unauthorized']),
        };
    }

    public function logout()
    {
        Auth::guard('frontend')->logout();
        return redirect()->route('frontend.login');
    }


    public function showRegisterForm()
    {
        return view('frontend.auth.register');
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
            'phone'     => 'required|regex:/^[0-9+\-\(\)\s]+$/|max:20',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^])[A-Za-z\d@$!%*#?&^]{8,}$/'
            ],
        ], [
            'full_name.required'    => __('messages.full_name_required'),
            'email.required'        => __('messages.email_required'),
            'email.email'           => __('messages.valid_email'),
            'email.unique'          => __('messages.email_already_exist'),
            'phone.required'        => __('messages.phone_required'),
            'phone.regex'           => __('messages.phone_regex'),
            'password.required'     => __('messages.password_required'),
            'password.min'          => __('messages.password_length'),
            'password.regex'        => __('messages.password_regex'),
            'password.confirmed'    => __('messages.password_confirmation_mismatch'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
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

        Auth::guard('frontend')->login($user); 

        return redirect()->route('user.dashboard'); // adjust route
    }

    public function showForgotPasswordForm()
    {
        return view('frontend.auth.forgot_password');
    }

    public function resetPassword(Request $request)
    {
        $email = $request->has('email') ? $request->email : '';
        if($email){
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->withErrors(['email' => __('messages.email_not_found')])->withInput();
            }else{
                $otp = rand(1000, 9999);
                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now()->addMinutes(10);
                $user->save();
                $user->notify(new ForgotPassword($user));

                session(['reset_email' => $email]);

                return redirect()->route('otp.enter')->with('success', __('messages.otp_send'));
            }
        }else{
            return back()->withErrors(['email' => __('messages.email_required')])->withInput();
        }
    }

    public function showOtpForm()
    {
        return view('frontend.auth.enter_otp');
    }

    public function verifyOtp(Request $request)
    {
        $code = $request->has('otp') ? $request->otp : '';
        $email = session('reset_email') ?? '';
        if($code){
            $user = User::where('email', $email)->where('otp', $code)->first();
            if (!$user) {
                return redirect()->back()->with('error', __('messages.invalid_code'));
            }else{
                if (Carbon::parse($user->otp_expires_at)->isPast()) {
                    return redirect()->back()->with('error', __('messages.otp_expired'));
                }

                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                return redirect()->route('new-password')->with('success', __('messages.otp_verified_successfully'));
            }
        }else{
            return redirect()->back()->with('error', __('messages.otp_required'));
        }
    }

    public function resendOtp(Request $request){

        $email = session('reset_email') ?? '';

        if($email){
            
            $user = User::where('email', $email)->first();
        
            if (!$user) {
                return redirect()->route('frontend.forgot-password');
            }else{
                $otp = rand(1000, 9999);
                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now()->addMinutes(10);
                $user->save();
                $user->notify(new ForgotPassword($user));

                return redirect()->route('otp.enter')->with('success', __('messages.otp_send'));
            }
        }else{
            return redirect()->route('frontend.forgot-password');
        }
    }

    public function newPasswordForm()
    {
        return view('frontend.auth.new_password');
    }

    public function submitNewPassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^])[A-Za-z\d@$!%*#?&^]{8,}$/'
            ],
        ], [
            'password.required'     => __('messages.password_required'),
            'password.min'          => __('messages.password_length'),
            'password.regex'        => __('messages.password_regex'),
            'password.confirmed'    => __('messages.password_confirmation_mismatch'),
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('frontend.login')->with('error', __('frontend.session_expired'));
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('frontend.login')->with('error', __('messages.email_not_found'));
        }

        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget('reset_email');

        return redirect()->route('frontend.login')->with('success', __('frontend.password_updated_successfully'));
    }
}

