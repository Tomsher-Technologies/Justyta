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
use Illuminate\Support\Facades\Storage;
use App\Notifications\ForgotPassword;
use App\Models\User;
use App\Models\Vendor;
use App\Models\MembershipPlan;
use App\Models\VendorTranslation;
use App\Models\VendorSubscription;
use App\Mail\CommonMail;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Carbon\Carbon;
use DB;

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

        if ($user->banned === 1) {
            return back()->withErrors(['password' => __('messages.account_disabled_deleted')]);
        }

        if ($user->user_type === 'vendor' && $user->approved === 0) {
            return back()->withErrors(['password' => __('messages.account_not_approved')]);
        }

        Auth::guard('frontend')->login($user);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
        session(['locale' => $user->language]);
       
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
                'min:6',
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
            'approved' => 1
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

        return redirect()->route('user.dashboard');
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

    public function showLawfirmRegisterForm()
    {
        $plans = MembershipPlan::where('is_active', 1)->get();
        return view('frontend.auth.law-firm-register', compact('plans'));
    }

    public function registerLawfirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'law_firm_name'                     => 'required',
            'email'                             => 'required|email',
            'phone'                             => 'required|string|max:20',
            'owner_name'                        => 'required|string|max:255',
            'owner_email'                       => ['required', 'email', Rule::unique('users', 'email')
                                                            ->where('user_type', 'vendor'),
                                                    ],
            'owner_phone'                       => 'required|string|max:20',
            'logo'                              => 'nullable|image|mimes:jpg,jpeg,png|max:200',
            'emirate_id'                        => 'required',
            'trn'                               => 'required',
            'firm_description'                  => 'required',
            'location'                          => 'required',
            'country'                           => 'nullable|string|max:255',
            'subscription_plan_id'              => 'required',
            'password'                          => 'required|string|min:6|confirmed',
            'trade_license'                     => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'trade_license_expiry'              => 'nullable|date',
            'emirates_id_front'                 => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_back'                  => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'emirates_id_expiry'                => 'required|date',
            'residence_visa'                    => 'nullable|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'residence_visa_expiry'             => 'nullable|date',
            'passport'                          => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'passport_expiry'                   => 'required|date',
            'card_of_law'                       => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'card_of_law_expiry'                => 'required|date',
            'ministry_of_justice_card'          => 'required|file|mimes:jpg,jpeg,png,svg,pdf,webp|max:200',
            'ministry_of_justice_card_expiry'   => 'required|date',
            'terms'                             => 'required',
        ],[
            '*.required'                            => __('frontend.this_field_required'),
            'email.email'                           => __('messages.valid_email'),
            'email.unique'                          => __('messages.email_already_exist'),
            'phone.regex'                           => __('messages.phone_regex'),
            'owner_email.email'                     => __('messages.valid_email'),
            'owner_email.unique'                    => __('messages.email_already_exist'),
            'owner_phone.regex'                     => __('messages.phone_regex'),
            'password.string'                       => __('messages.password_regex'),
            'password.min'                          => __('messages.password_length'),
            'password.regex'                        => __('messages.password_regex'),
            'password.confirmed'                    => __('messages.password_confirmation_mismatch'),
            'logo.image'                            => __('frontend.allowed_files'),
            'logo.mimes'                            => __('frontend.allowed_files'),
            'logo.max'                              => __('frontend.max_file_size', ['size' => '2MB']),
            'trade_license.file'                    => __('frontend.allowed_files'),
            'trade_license.mimes'                   => __('frontend.allowed_files'),
            'trade_license.max'                     => __('frontend.max_file_size', ['size' => '2MB']),
            'emirates_id_front.file'                => __('frontend.allowed_files'),
            'emirates_id_front.mimes'               => __('frontend.allowed_files'),
            'emirates_id_front.max'                 => __('frontend.max_file_size', ['size' => '2MB']),
            'emirates_id_back.file'                 => __('frontend.allowed_files'),
            'emirates_id_back.mimes'                => __('frontend.allowed_files'),
            'emirates_id_back.max'                  => __('frontend.max_file_size', ['size' => '2MB']),
            'residence_visa.file'                   => __('frontend.allowed_files'),
            'residence_visa.mimes'                  => __('frontend.allowed_files'),
            'residence_visa.max'                    => __('frontend.max_file_size', ['size' => '2MB']),
            'passport.file'                         => __('frontend.allowed_files'),
            'passport.mimes'                        => __('frontend.allowed_files'),
            'passport.max'                          => __('frontend.max_file_size', ['size' => '2MB']),
            'card_of_law.file'                      => __('frontend.allowed_files'),
            'card_of_law.mimes'                     => __('frontend.allowed_files'),
            'card_of_law.max'                       => __('frontend.max_file_size', ['size' => '2MB']),
            'ministry_of_justice_card.file'         => __('frontend.allowed_files'),
            'ministry_of_justice_card.mimes'        => __('frontend.allowed_files'),
            'ministry_of_justice_card.max'          => __('frontend.max_file_size', ['size' => '2MB']),
            'trade_license_expiry.date'             => __('frontend.valid_date'),
            'emirates_id_expiry.date'               => __('frontend.valid_date'),
            'residence_visa_expiry.date'            => __('frontend.valid_date'),
            'passport_expiry.date'                  => __('frontend.valid_date'),
            'card_of_law_expiry.date'               => __('frontend.valid_date'),
            'ministry_of_justice_card_expiry.date'  => __('frontend.valid_date'), 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $user = User::create([
            'name' => $request->law_firm_name,
            'email' => $request->owner_email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'user_type' => 'vendor',
        ]);

        $vendor = new Vendor([
            'consultation_commission'   => 0,
            'law_firm_name'             => $request->law_firm_name, 
            'law_firm_email'            => $request->email, 
            'law_firm_phone'            => $request->phone, 
            'office_address'            => $request->location,
            'owner_name'                => $request->owner_name, 
            'owner_email'               => $request->owner_email,  
            'owner_phone'               => $request->owner_phone,  
            'emirate_id'                => $request->emirate_id, 
            'trn'                       => $request->trn, 
            'website_url'               => $request->website_url,
            'logo'                      => $request->hasfile('logo') ? uploadImage('vendors/'.$user->id, $request->logo, 'logo_') : NULL,  
            'country' => 'UAE', 
            'trade_license'             => $request->hasfile('trade_license') ? uploadImage('vendors/'.$user->id, $request->trade_license, 'trade_license_') : NULL,
            'trade_license_expiry'      => $request->trade_license_expiry ? Carbon::parse($request->trade_license_expiry)->format('Y-m-d') : null,
            'emirates_id_front'         => $request->hasfile('emirates_id_front') ? uploadImage('vendors/'.$user->id, $request->emirates_id_front, 'emirates_id_front_') : NULL,
            'emirates_id_back'          => $request->hasfile('emirates_id_back') ? uploadImage('vendors/'.$user->id, $request->emirates_id_back, 'emirates_id_back_') : NULL,
            'emirates_id_expiry'        => $request->emirates_id_expiry ? Carbon::parse($request->emirates_id_expiry)->format('Y-m-d') : null,
            'residence_visa'            => $request->hasfile('residence_visa') ? uploadImage('vendors/'.$user->id, $request->residence_visa, 'residence_visa_') : NULL,
            'residence_visa_expiry'     => $request->residence_visa_expiry ? Carbon::parse($request->residence_visa_expiry)->format('Y-m-d') : null,
            'passport'                  => $request->hasfile('passport') ? uploadImage('vendors/'.$user->id, $request->passport, 'passport_') : NULL,
            'passport_expiry'           => $request->passport_expiry ? Carbon::parse($request->passport_expiry)->format('Y-m-d') : null,
            'card_of_law'               => $request->hasfile('card_of_law') ? uploadImage('vendors/'.$user->id, $request->card_of_law, 'card_of_law_') : NULL,
            'card_of_law_expiry'        => $request->card_of_law_expiry ? Carbon::parse($request->card_of_law_expiry)->format('Y-m-d') : null,
            'ministry_of_justice_card'  => $request->hasfile('ministry_of_justice_card') ? uploadImage('vendors/'.$user->id, $request->ministry_of_justice_card, 'ministry_of_justice_card_') : NULL,
            'ministry_of_justice_card_expiry'=> $request->ministry_of_justice_card_expiry ? Carbon::parse($request->ministry_of_justice_card_expiry)->format('Y-m-d') : null,
        ]);

        $user->vendor()->save($vendor);
        $plan = MembershipPlan::findOrFail($request->subscription_plan_id);

        $vendorTrans = VendorTranslation::create([
            'vendor_id' => $vendor->id,
            'lang' => 'en',
            'law_firm_name' => $request->law_firm_name,
            'about' => $request->firm_description
        ]);
       
        $totalAmount = $plan->amount;
        if($totalAmount != 0){
            $orderReference = $vendor->id .'--'.$vendor->ref_no;
            $customer = [
                            'email' => $user->email,
                            'name'  => $user->name,
                            'phone' => $user->phone
                        ];
            $payment = createWebPlanOrder($customer, $totalAmount, env('APP_CURRENCY','AED'), $orderReference);

            if (isset($payment['_links']['payment']['href'])) {
                $vendor->subscriptions()->create([
                    'membership_plan_id'                => $plan->id,
                    'amount'                            => $plan->amount,
                    'member_count'                      => $plan->member_count,
                    'job_post_count'                    => $plan->job_post_count,
                    'en_ar_price'                       => $plan->en_ar_price,
                    'for_ar_price'                      => $plan->for_ar_price,
                    'live_online'                       => $plan->live_online,
                    'specific_law_firm_choice'          => $plan->specific_law_firm_choice,
                    'annual_legal_contract'             => $plan->annual_legal_contract,
                    'annual_free_ad_days'               => $plan->annual_free_ad_days,
                    'unlimited_training_applications'   => $plan->unlimited_training_applications,
                    'welcome_gift'                      => $plan->welcome_gift,
                    'subscription_start'                => now(),
                    'subscription_end'                  => now()->addYear(), 
                    'status'                            => 'pending',
                    'payment_reference'                 => $payment['reference'] ?? null,
                ]);
                return redirect()->away($payment['_links']['payment']['href']);
            }

            return redirect()->back()->with('error', 'Failed to initiate payment');
        }else{
            $vendor->subscriptions()->create([
                'membership_plan_id'                => $plan->id,
                'amount'                            => $plan->amount,
                'member_count'                      => $plan->member_count,
                'job_post_count'                    => $plan->job_post_count,
                'en_ar_price'                       => $plan->en_ar_price,
                'for_ar_price'                      => $plan->for_ar_price,
                'live_online'                       => $plan->live_online,
                'specific_law_firm_choice'          => $plan->specific_law_firm_choice,
                'annual_legal_contract'             => $plan->annual_legal_contract,
                'annual_free_ad_days'               => $plan->annual_free_ad_days,
                'unlimited_training_applications'   => $plan->unlimited_training_applications,
                'welcome_gift'                      => $plan->welcome_gift,
                'subscription_start'                => now(),
                'subscription_end'                  => now()->addYear(), 
                'status'                            => 'active',
            ]);


            $array['subject'] = 'Registration Successful - Welcome to '.env('APP_NAME','Justyta').'!';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['content'] = "Hi $request->owner_name, <p> Congratulations and welcome to ".env('APP_NAME')."! We are delighted to inform you that your registration has been successfully completed. Thank you for choosing us as your trusted partner. We're excited to have your law firm onboard.</p>

                <p>Here are your registration details:</p>

                <ul>
                <li><strong>Firm Name : </strong> $request->law_firm_name </li>
                <li><strong>Registered Email : </strong> $request->email </li>
                <li><strong>Plan : </strong> $plan->title </li>
                <li><strong>Plan Expiry Date : </strong> ".now()->addYear()." </li>
                </ul>
                <p>Thank you for choosing ".env('APP_NAME').". </p><hr>
                <p style='font-size: 12px; color: #777;'>
                    This email was sent to $user->email. If you did not register on our platform, please ignore this message.
                </p>";
            Mail::to($user->email)->queue(new CommonMail($array));

            session()->flash('success', 'Account created successfully. Please wait for the admin approval. You will be notified via email. Thank you.');

            return redirect()->route('frontend.login'); 
        }
    }

    public function purchaseSuccess(Request $request) 
    {
        $paymentReference = $request->query('ref') ?? NULL;
        $token = getAccessToken();

        $baseUrl = config('services.ngenius.base_url');
        $outletRef = config('services.ngenius.outlet_ref');

        $response = Http::withToken($token)->get("{$baseUrl}/transactions/outlets/" . $outletRef . "/orders/{$paymentReference}");
        $data = $response->json();
      
        $orderRef = $data['merchantOrderReference'] ?? NULL;
        $subscriptionData = explode('--', $orderRef);

        $vendorID = $subscriptionData[0];
        
        $status = $data['_embedded']['payment'][0]['state'] ?? null;
        $paid_amount = $data['_embedded']['payment'][0]['amount']['value'] ?? 0;

        $paidAmount = ($paid_amount != 0) ? $paid_amount/100 : 0;
        $vendor = Vendor::findOrFail($vendorID);

        if ($status === 'PURCHASED' || $status === 'CAPTURED') {
            $subscription = VendorSubscription::where('vendor_id', $vendorID)->where('status', 'pending')->first();
            if ($subscription) {
                $subscription->status = 'active';
                $subscription->subscription_start = now();
                $subscription->subscription_end = now()->addYear();
                $subscription->amount = $paidAmount;
                $subscription->save();
            }

            $plan = MembershipPlan::findOrFail($subscription->membership_plan_id);

            $array['subject'] = 'Registration Successful - Welcome to '.env('APP_NAME','Justyta').'!';
            $array['from'] = env('MAIL_FROM_ADDRESS');
            $array['content'] = "Hi $vendor->owner_name, <p> Congratulations and welcome to ".env('APP_NAME')."! We are delighted to inform you that your registration has been successfully completed. Thank you for choosing us as your trusted partner. We're excited to have your law firm onboard.</p>

                <p>Here are your registration details:</p>

                <ul>
                <li><strong>Firm Name : </strong> $vendor->law_firm_name </li>
                <li><strong>Registered Email : </strong> $vendor->owner_email </li>
                <li><strong>Plan : </strong> $plan->title ?? '' </li>
                <li><strong>Plan Expiry Date : </strong> ".now()->addYear()." </li>
                </ul>
                <p>Thank you for choosing ".env('APP_NAME').". </p><hr>
                <p style='font-size: 12px; color: #777;'>
                    This email was sent to $vendor->owner_email. If you did not register on our platform, please ignore this message.
                </p>";
            Mail::to($vendor->owner_email)->queue(new CommonMail($array));

            session()->flash('success', '{{ __("frontend.vendor_registration_success") }}');

            return redirect()->route('frontend.login'); 
        }else{
            $user_id = $vendor->user_id;
            $user = User::find($user_id);
            $user->forceDelete();

            $folderPath = "vendors/{$user_id}";

            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            session()->flash('error',  __("frontend.vendor_registration_failed"));
            
            return redirect()->route('law-firm.register');
        }
    }

    public function purchaseCancel(Request $request){
        $ref = $request->get('ref'); 

        if (!$ref) {
            return redirect()->route('frontend.login');
        }
        $subscription = VendorSubscription::where('payment_reference', $ref)->first();

        if ($subscription) {
            $vendorID = $subscription->vendor_id;

            $vendor = Vendor::find($vendorID);
            $user_id = $vendor->user_id;
            $user = User::find($user_id);
            $user->forceDelete();

            $folderPath = "vendors/{$user_id}";

            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }
        }
        session()->flash('error',  __("frontend.vendor_registration_failed") );
        return redirect()->route('law-firm.register');
    }
}

