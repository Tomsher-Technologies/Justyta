@extends('layouts.web_login', ['title' => __('frontend.sign_up')])

@section('content')
    <section class="px-[20px] py-[20px] md:px-[100px] md:py-[80px]">
        <div class="w-full p-8 space-y-6 p-5 bg-white rounded-lg ">
            <form action="{{ route('law-firm.register.submit') }}" id="signupForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-8">
                    <div class="flex items justify-between">
                        <h2 class="text-3xl text-black">{{ __('frontend.sign_up') }}</h2>
                        <a href="{{ route('frontend.login') }}"
                            class="text-[#B9A572] underline">{{ __('frontend.back_to_login') }}</a>
                    </div>

                    <hr class="my-8 border-gray-300">

                    <div>
                        <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.law_firm_information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.law_firm_name') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="law_firm_name" id="law_firm_name"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('law_firm_name')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirate') }}<span
                                        class="text-red-500">*</span></label>
                                <select name="emirate_id" id="emirate_id"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                    <option value="">{{ __('frontend.choose_option') }}</option>
                                    @foreach (\App\Models\Emirate::with('translations')->get() as $emirate)
                                        <option value="{{ $emirate->id }}"
                                            {{ old('emirate_id') == $emirate->id ? 'selected' : '' }}>
                                            {{ $emirate->getTranslation('name', app()->getLocale() ?? 'en') }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('emirate_id')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.location') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="location" id="location"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('location')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone_number') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="phone" id="phone"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('phone')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('email')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.website_url') }}</label>
                                <input type="url" name="website_url" id="website_url"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('website_url')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.trade_license') }}</label>
                                <input type="file" name="trade_license" id="trade_licenseInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('trade_license')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="trade_licensePreview" class="mt-2" style="display:none;">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.trade_license_expiry') }}</label>
                                <input type="date" name="trade_license_expiry" placeholder="d M Y"
                                    value="{{ old('trade_license_expiry') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('trade_license_expiry')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.vat_number') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="trn" id="trn" value="{{ old('trn') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('trn')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.firm_description') }}<span
                                        class="text-red-500">*</span></label>
                                <textarea name="firm_description" id="firm_description"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 h-24"
                                    placeholder="{{ __('frontend.enter') }}">{{ old('firm_description') }}</textarea>
                                <span class="text-[#717171] text-sm">0/1000</span>
                                @error('firm_description')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Owner Information -->

                    <hr class="my-8 border-gray-300">

                    <div>
                        <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.owner_information') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.full_name') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="owner_name" id="owner_name" value="{{ old('owner_name') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('owner_name')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="owner_email" id="owner_email"
                                    value="{{ old('owner_email') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('owner_email')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="owner_phone" id="owner_phone"
                                    value="{{ old('owner_phone') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                    placeholder="{{ __('frontend.enter') }}">
                                @error('owner_phone')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_front') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="file" name="emirates_id_front" id="emirates_id_frontInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('emirates_id_front')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="emirates_id_frontPreview" class="mt-2" style="display:none;">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_back') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="file" name="emirates_id_back" id="emirates_id_backInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('emirates_id_back')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="emirates_id_backPreview" class="mt-2" style="display:none;">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.emirates_id_expiry') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="emirates_id_expiry" id="emirates_id_expiry"
                                    value="{{ old('emirates_id_expiry') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('emirates_id_expiry')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="w-full">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.passport') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="file" name="passport" id="passportInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('passport')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="passportPreview" class="mt-2" style="display:none;"></div>
                            </div>
                            <div class="w-full">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.passport_expiry') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="passport_expiry" id="passport_expiry"
                                    value="{{ old('passport_expiry') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('passport_expiry')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residence_visa') }}</label>
                                <input type="file" name="residence_visa" id="residence_visaInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('residence_visa')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="residence_visaPreview" class="mt-2" style="display:none;">
                                </div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.residence_visa_expiry') }}</label>
                                <input type="date" name="residence_visa_expiry" id="residence_visa_expiry"
                                    value="{{ old('residence_visa_expiry') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('residence_visa_expiry')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.dubai_lawyer_card') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="file" name="card_of_law" id="card_of_lawInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('card_of_law')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="card_of_lawPreview" class="mt-2" style="display:none;"></div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.dubai_lawyer_card_expiry') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="card_of_law_expiry" id="card_of_law_expiry"
                                    value="{{ old('card_of_law_expiry') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('card_of_law_expiry')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.ministry_of_justice_card') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="file" name="ministry_of_justice_card" id="ministry_of_justice_cardInput"
                                    accept="image/*,application/pdf"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full">
                                @error('ministry_of_justice_card')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                                <div id="ministry_of_justice_cardPreview" class="mt-2" style="display:none;"></div>
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.ministry_of_justice_card_expiry') }}<span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="ministry_of_justice_card_expiry"
                                    id="ministry_of_justice_card_expiry"
                                    value="{{ old('ministry_of_justice_card_expiry') }}"
                                    class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                                @error('ministry_of_justice_card_expiry')
                                    <div class="text-sm text-red-500">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-8 border-gray-300">
                    <!-- Membership Plan & Login -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        <div>
                            <h3 class="text-xl font-medium text-[#07683B] mb-4">
                                {{ __('frontend.choose_membership_plan') }}</h3>
                            <div class="flex items-center justify-between mb-1">
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.membership_plan') }}<span
                                        class="text-red-500">*</span></label>
                                <a href="#" class="text-[#B9A572] underline text-sm" data-modal-target="plan-modal"
                                    data-modal-toggle="plan-modal">{{ __('frontend.view_plans') }}</a>
                            </div>
                            <select name="subscription_plan_id" id="subscription_plan_id"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2">
                                <option value="">{{ __('frontend.choose_option') }}</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" data-amount="{{ $plan->amount }}"
                                        {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->title }}</option>
                                @endforeach
                            </select>
                            @error('subscription_plan_id')
                                <div class="text-sm text-red-500">{{ $message }}</div>
                            @enderror
                            <div class="mb-2">{{ __('frontend.membership_amount') }} : <b class="text-[#07683B]"
                                    id="membership_amount">AED 0.00</b></div>

                        </div>
                        <div>
                            <h3 class="text-xl font-medium text-[#07683B] mb-4">{{ __('frontend.set_up_login_details') }}
                            </h3>
                            <div class="flex items-center gap-5">
                                <div class="w-full">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.password') }}<span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password" id="password" autocomplete="new-password"
                                        class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2"
                                        placeholder="{{ __('frontend.enter') }}">

                                    @error('password')
                                        <div class="text-sm text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="w-full">
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.confirm_password') }}<span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 mb-2"
                                        placeholder="{{ __('frontend.enter') }}">
                                    @error('password_confirmation')
                                        <div class="text-sm text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>
                            <ul class="text-xs text-gray-500 list-disc pl-5 mb-2">
                                <li>{{ __('frontend.min_characters') }}</li>
                                <li>{{ __('frontend.atleast_case') }}</li>
                                <li>{{ __('frontend.atleast_digit_special') }}</li>
                            </ul>

                        </div>
                    </div>

                    <div class="items-center mb-4">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="terms" id="terms" value="1" class="mr-2" />
                            <label for="terms" class="text-sm">
                                {!! __('frontend.agree_terms', [
                                    'terms' => '<a href="'.route('terms-conditions').'" class="underline text-[#B9A572]">' . __('frontend.terms') . '</a>',
                                ]) !!}
                            </label>
                        </div>
                        @error('terms')
                            <div class="text-sm text-red-500">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit"
                        class="bg-[#04502E] text-white px-8 py-3 rounded-lg">{{ __('frontend.sign_up') }}</button>
                </div>
            </form>


            <div id="plan-modal" tabindex="-1" aria-hidden="true"
                class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                <div class="relative p-4 w-full max-w-5xl max-h-full">
                    <!-- Modal content -->
                    <div class="relative bg-white rounded-lg shadow-sm">
                        <!-- Modal header -->
                        <div class="flex items-center justify-end p-4 pb-0">
                            <button type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                                data-modal-hide="plan-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <div class="p-4 md:p-5 space-y-4">
                            <section class="py-10 px-4 md:px-16 pt-0">
                                <div class="mb-10 text-center">
                                    <h3 class="text-3xl font-medium text-gray-800">{{ __('frontend.membership_plans') }}</h3>
                                    <p class="text-[#52575D]">{{ __('frontend.choose_plan_description') }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    @foreach ($plans as $plan)
                                        <div class="bg-white rounded-2xl border border-gray-300 p-6 flex flex-col h-full">
                                            <!-- Header -->
                                            <div class="flex items-start justify-between mb-4">
                                                <div>
                                                    <h2 class="text-xl font-medium text-gray-800">{{ $plan->getTranslation('title') }}
                                                    </h2>
                                                    <p class="text-2xl font-semibold text-gray-800 mb-2">
                                                        {{ number_format($plan->amount, 2) }} {{ __('frontend.AED') }}
                                                    </p>
                                                    {!! $plan->is_active
                                                        ? '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>'
                                                        : '<span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Inactive</span>' !!}
                                                </div>
                                                @if ($plan->icon)
                                                    <img src="{{ asset(getUploadedImage($plan->icon)) }}"
                                                        alt="{{ $plan->getTranslation('title') }}" class="h-16 w-16 object-contain">
                                                @endif
                                            </div>

                                            <!-- Features -->
                                            <ul class="space-y-2 text-sm text-gray-600 flex-1">
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    {{ $plan->live_online ? __('frontend.live_online_access') : '-' }}
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    {{ $plan->specific_law_firm_choice ? __('frontend.specific_law_firm_choice') : '-' }}
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    {{ $plan->annual_legal_contract ? __('frontend.annual_legal_contract') : '-' }}
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    {{ __('frontend.up_to') }} {{ $plan->member_count }} {{ __('frontend.user_access') }}
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    {{ $plan->job_post_count === 0 ? __('frontend.unlimited_job_posts') : $plan->job_post_count . ' '. __('frontend.job_posts_month') }}
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    @if ($plan->annual_free_ad_days != 0)
                                                        {{ $plan->annual_free_ad_days }} {{ __('frontend.free_ad_days') }}
                                                    @else
                                                        -
                                                    @endif
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    {{ $plan->unlimited_training_applications ? __('frontend.unlimited_training_applications') : '-' }}
                                                </li>
                                                <li class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 mt-1 text-green-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z" />
                                                    </svg>
                                                    @if ($plan->welcome_gift === 'premium')
                                                        {{ __('frontend.premium_welcome_gift') }}
                                                    @elseif($plan->welcome_gift === 'special')
                                                        {{ __('frontend.special_welcome_gift') }}
                                                    @else
                                                        {{ __('frontend.no_welcome_gift') }}
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('script')
    <script>
        document.getElementById('subscription_plan_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let amount = selectedOption.getAttribute('data-amount') || 0;
            document.getElementById('membership_amount').textContent = 'AED ' + parseFloat(amount).toFixed(2);
        });

        function setupFilePreview(inputId, previewId) {
            const input = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewId);

            if (!input || !previewContainer) return;

            input.addEventListener('change', function() {
                const file = this.files[0];

                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';

                if (file) {
                    const fileType = file.type;

                    if (fileType.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.className = 'img-thumbnail mt-2';
                        img.style.maxHeight = '150px';

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            img.src = e.target.result;
                            previewContainer.innerHTML = '';
                            previewContainer.appendChild(img);
                            previewContainer.style.display = 'block';
                        }
                        reader.readAsDataURL(file);
                    } else if (fileType === 'application/pdf') {
                        const object = document.createElement('embed');
                        object.src = URL.createObjectURL(file);
                        object.type = 'application/pdf';
                        object.width = '100%';
                        object.height = '150px';
                        object.className = 'mt-2 border';

                        previewContainer.innerHTML = '';
                        previewContainer.appendChild(object);
                        previewContainer.style.display = 'block';
                    }
                }
            });
        }

        setupFilePreview('logoInput', 'logoPreview');
        setupFilePreview('emirates_id_frontInput', 'emirates_id_frontPreview');
        setupFilePreview('emirates_id_backInput', 'emirates_id_backPreview');
        setupFilePreview('ministry_of_justice_cardInput', 'ministry_of_justice_cardPreview');
        setupFilePreview('residence_visaInput', 'residence_visaPreview');
        setupFilePreview('passportInput', 'passportPreview');
        setupFilePreview('card_of_lawInput', 'card_of_lawPreview');
        setupFilePreview('trade_licenseInput', 'trade_licensePreview');

        $(document).ready(function() {

            $.validator.addMethod("filesize", function(value, element, param) {
                if (value === "") return true;
                if (element.files.length === 0) return false;
                return element.files[0].size <= param;
            }, "File size must be less than {0} bytes.");

            $.validator.addMethod("fileext", function(value, element, param) {
                if (value === "") return true;
                if (element.files.length === 0) return false;
                var ext = value.split('.').pop().toLowerCase();
                var allowed = param.split(',').map(function(v) {
                    return v.trim().toLowerCase();
                });
                return allowed.indexOf(ext) !== -1;
            }, "{{ __('frontend.invalid_file_type') }}");

            $.validator.addMethod("strongPassword", function(value, element) {
                return this.optional(element) ||
                    /[A-Z]/.test(value) &&
                    /[a-z]/.test(value) &&
                    /[0-9]/.test(value) &&
                    /[^A-Za-z0-9]/.test(value) &&
                    value.length >= 8;
            }, "{{ __('frontend.strong_password') }}");

            // phone simple pattern (+ optional) 7-15 digits
            $.validator.addMethod("phonePattern", function(value, element) {
                return this.optional(element) || /^[+]?[0-9]{7,15}$/.test(value);
            }, "{{ __('messages.phone_regex') }}");

            $("#signupForm").validate({
                ignore: [],
                rules: {
                    law_firm_name: {
                        required: true
                    },
                    emirate_id: {
                        required: true
                    },
                    location: {
                        required: true
                    },
                    phone: {
                        required: true,
                        phonePattern: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    trn: {
                        required: true
                    },
                    firm_description: {
                        required: true,
                        maxlength: 1000
                    },
                    trade_license: {
                        required: false,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    residence_visa: {
                        required: false,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    emirates_id_front: {
                        required: true,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    emirates_id_back: {
                        required: true,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    emirates_id_expiry: {
                        required: true,
                        dateISO: true
                    },
                    passport: {
                        required: true,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    passport_expiry: {
                        required: true,
                        dateISO: true
                    },
                    card_of_law: {
                        required: true,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    card_of_law_expiry: {
                        required: true,
                        dateISO: true
                    },
                    ministry_of_justice_card: {
                        required: true,
                        fileext: "jpg,jpeg,png,svg,pdf,webp",
                        filesize: 102400
                    },
                    ministry_of_justice_card_expiry: {
                        required: true,
                        dateISO: true
                    },
                    subscription_plan_id: {
                        required: true
                    },
                    password: {
                        required: true,
                        strongPassword: true
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    },
                    owner_name: {
                        required: true
                    },
                    owner_email: {
                        required: true,
                        email: true
                    },
                    owner_phone: {
                        required: true,
                        phonePattern: true
                    },
                    terms: {
                        required: true
                    }
                },
                messages: {
                    law_firm_name: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    emirate_id: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    location: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    phone: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    email: {
                        required: "{{ __('frontend.this_field_required') }}",
                        email: "{{ __('messages.valid_email') }}"
                    },
                    trn: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    firm_description: {
                        required: "{{ __('frontend.this_field_required') }}",
                        maxlength: "{{ __('frontend.max_length', ['length' => 1000]) }}"
                    },
                    emirates_id_front: {
                        required: "{{ __('frontend.this_field_required') }}",
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    emirates_id_back: {
                        required: "{{ __('frontend.this_field_required') }}",
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    emirates_id_expiry: {
                        required: "{{ __('frontend.this_field_required') }}",
                        dateISO: "{{ __('frontend.valid_date') }}"
                    },
                    passport: {
                        required: "{{ __('frontend.this_field_required') }}",
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    passport_expiry: {
                        required: "{{ __('frontend.this_field_required') }}",
                        dateISO: "{{ __('frontend.valid_date') }}"
                    },
                    card_of_law: {
                        required: "{{ __('frontend.this_field_required') }}",
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    card_of_law_expiry: {
                        required: "{{ __('frontend.this_field_required') }}",
                        dateISO: "{{ __('frontend.valid_date') }}"
                    },
                    trade_license: {
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    residence_visa: {
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    ministry_of_justice_card: {
                        required: "{{ __('frontend.this_field_required') }}",
                        fileext: "{{ __('frontend.allowed_files') }}",
                        filesize: "{{ __('frontend.max_file_size', ['size' => '100MB']) }}"
                    },
                    ministry_of_justice_card_expiry: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    subscription_plan_id: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    password: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    password_confirmation: {
                        required: "{{ __('frontend.this_field_required') }}",
                        equalTo: "{{ __('messages.password_confirmation_mismatch') }}"
                    },
                    owner_name: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    owner_email: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    owner_phone: {
                        required: "{{ __('frontend.this_field_required') }}"
                    },
                    terms: {
                        required: "{{ __('frontend.this_field_required') }}"
                    }
                },
                errorElement: 'div',
                errorClass: 'text-sm text-red-500 mt-1',
                errorPlacement: function(error, element) {
                    error.addClass('text-red-500 text-sm');

                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2'));
                    } else if (element.attr("type") === "checkbox") {
                        error.insertAfter(element.closest('label'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2').find('.select2-selection')
                            .addClass('border-red-500');
                    } else {
                        $(element).addClass('border-red-500');
                    }
                },
                unhighlight: function(element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2').find('.select2-selection')
                            .removeClass('border-red-500');
                    } else {
                        $(element).removeClass('border-red-500');
                    }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        });
    </script>
@endsection
