@extends('layouts.web_login', ['title' => 'Login In'])

@section('content')
    <section class="bg-[#FFF7F0] px-[100px] py-[80px] pt-0">
        <div class="flex items-center justify-center">
            <div class="w-full max-w-lg p-8 space-y-6 p-5 bg-white rounded-lg">
                <h2 class="text-3xl font-semibold text-gray-900 mb-8">{{ __('frontend.sign_up') }}</h2>

                @php
                    $lang = app()->getLocale() ?? 'en';
                    $contentDynamic = getPageDynamicContent('register_page', $lang);
                @endphp
                <p class="text-xs text-gray-600">
                    {{ $contentDynamic['content'] ?? '' }}
                </p>

                

                <form class="space-y-4" method="POST" action="{{ route('frontend.register.submit') }}">
                    @csrf
                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.full_name') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative mb-2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21"
                                    fill="none">
                                    <path
                                        d="M9.99935 8.91215C11.8403 8.91215 13.3327 7.41977 13.3327 5.57882C13.3327 3.73787 11.8403 2.24548 9.99935 2.24548C8.1584 2.24548 6.66602 3.73787 6.66602 5.57882C6.66602 7.41977 8.1584 8.91215 9.99935 8.91215Z"
                                        stroke="#7B7B7B" stroke-width="1.5" />
                                    <path
                                        d="M16.6663 15.1621C16.6663 17.2329 16.6663 18.9121 9.99967 18.9121C3.33301 18.9121 3.33301 17.2329 3.33301 15.1621C3.33301 13.0913 6.31801 11.4121 9.99967 11.4121C13.6813 11.4121 16.6663 13.0913 16.6663 15.1621Z"
                                        stroke="#7B7B7B" stroke-width="1.5" />
                                </svg>
                            </div>
                            <input type="text" id="full_name" name="full_name"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 ps-10"
                                placeholder="{{ __('frontend.enter_full_name') }}"  value="{{ old('full_name') }}"/>
                        </div>
                        @error('full_name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative mb-2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21"
                                    fill="none">
                                    <path
                                        d="M14.7396 3.04785H5.98958C4.14863 3.04785 2.65625 4.54024 2.65625 6.38119V13.8812C2.65625 15.7221 4.14863 17.2145 5.98958 17.2145H14.7396C16.5805 17.2145 18.0729 15.7221 18.0729 13.8812V6.38119C18.0729 4.54024 16.5805 3.04785 14.7396 3.04785Z"
                                        stroke="#7B7B7B" stroke-width="1.5" />
                                    <path
                                        d="M2.69531 6.45605L8.69948 9.89772C9.20263 10.1897 9.77401 10.3435 10.3557 10.3435C10.9374 10.3435 11.5088 10.1897 12.012 9.89772L18.0328 6.45605"
                                        stroke="#7B7B7B" stroke-width="1.5" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </div>
                            <input type="email" id="email" name="email"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 ps-10"
                                placeholder="{{ __('frontend.enter_email') }}"   value="{{ old('email') }}"/>
                        </div>
                        @error('email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative mb-2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="26" viewBox="0 0 25 26"
                                    fill="none">
                                    <path
                                        d="M17.48 22.8819C19.3005 22.8819 20.5041 22.3899 21.5711 21.1971C21.655 21.1136 21.7282 21.0194 21.8121 20.9354C22.4398 20.2346 22.7327 19.5439 22.7327 18.885C22.7327 18.1314 22.2934 17.4305 21.3621 16.7814L18.3175 14.668C17.3755 14.0194 16.2769 13.9462 15.3979 14.8145L14.5925 15.6203C14.3514 15.8609 14.1425 15.8716 13.9019 15.7247C13.3474 15.3689 12.2068 14.3752 11.4322 13.6011C10.6162 12.7953 9.82107 11.8957 9.41303 11.2363C9.2666 10.9953 9.28758 10.7966 9.52821 10.556L10.3233 9.75018C11.2023 8.8716 11.1291 7.76223 10.4804 6.83143L8.35633 3.78678C7.71794 2.85509 7.01705 2.42607 6.26348 2.4158C5.60455 2.40509 4.91392 2.70866 4.21303 3.33634C4.11883 3.42027 4.03491 3.49348 3.94071 3.56669C2.75857 4.63366 2.2666 5.83723 2.2666 7.64705C2.2666 10.6399 4.10812 14.281 7.48803 17.6604C10.8465 21.0194 14.4983 22.8819 17.48 22.8819ZM17.4907 21.2703C14.8224 21.3225 11.4014 19.2721 8.69115 16.5725C5.96035 13.852 3.81526 10.3154 3.86749 7.6475C3.88848 6.49616 4.28624 5.50241 5.11303 4.7908C5.17553 4.7283 5.23803 4.67562 5.31124 4.62339C5.61482 4.35107 5.96035 4.20509 6.25321 4.20509C6.56705 4.20509 6.83892 4.31982 7.03803 4.63366L9.06794 7.6783C9.28758 8.00285 9.30857 8.36937 8.98401 8.69348L8.06348 9.61446C7.33089 10.3363 7.39383 11.2154 7.91705 11.9162C8.51303 12.7221 9.54919 13.8939 10.3443 14.689C11.1501 15.4948 12.4157 16.6247 13.2215 17.2314C13.9224 17.7546 14.8121 17.8073 15.534 17.085L16.4545 16.1645C16.7791 15.8399 17.1349 15.8609 17.459 16.0703L20.5037 18.1002C20.8179 18.3091 20.9434 18.5707 20.9434 18.885C20.9434 19.1886 20.797 19.5234 20.5148 19.8368C20.4656 19.9056 20.4132 19.972 20.3577 20.0359C19.6358 20.852 18.6416 21.2493 17.4907 21.2703Z"
                                        fill="#7B7B7B" />
                                </svg>
                            </div>
                            <input type="text" id="phone" name="phone"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 ps-10"
                                placeholder="{{ __('frontend.enter_phone_number') }}"   value="{{ old('phone') }}"/>
                        </div>
                        @error('phone')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.password') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21"
                                    fill="none">
                                    <path
                                        d="M7.11914 8.52962V6.02962C7.11914 5.14557 7.47033 4.29772 8.09545 3.6726C8.72057 3.04748 9.56842 2.69629 10.4525 2.69629C11.3365 2.69629 12.1844 3.04748 12.8095 3.6726C13.4346 4.29772 13.7858 5.14557 13.7858 6.02962V8.52962"
                                        stroke="#7B7B7B" stroke-width="1.5" stroke-linecap="round" />
                                    <path
                                        d="M4.61914 8.52979H16.2858V16.0298C16.2858 16.4718 16.1102 16.8957 15.7977 17.2083C15.4851 17.5209 15.0612 17.6965 14.6191 17.6965H6.28581C5.84378 17.6965 5.41986 17.5209 5.1073 17.2083C4.79474 16.8957 4.61914 16.4718 4.61914 16.0298V8.52979Z"
                                        stroke="#7B7B7B" stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M12.5352 13.1128H12.5435V13.1211H12.5352V13.1128Z" stroke="#7B7B7B"
                                        stroke-width="2.25" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <input type="password" id="password" name="password"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 ps-10"
                                placeholder="{{ __('frontend.enter_password') }}"   value="{{ old('password') }}"/>

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="togglePassword()">
                                <!-- Eye icon -->
                                <svg id="eye-icon" class="w-5 h-5 text-gray-400 block" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5
                                        c4.478 0 8.268 2.943 9.542 7
                                        -1.274 4.057-5.064 7-9.542 7
                                        -4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <!-- Eye-off icon -->
                                <svg id="eye-off-icon" class="w-5 h-5 text-gray-400 hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7
                                        a9.957 9.957 0 012.122-3.362M9.88 9.88a3 3 0 104.242 4.242
                                        M7.5 7.5L3 3m18 18l-4.5-4.5M14.121 14.121L17 17" />
                                </svg>
                            </div>
                        </div>
                        <ul class="text-xs text-gray-500 mt-3 space-y-1 mb-2">
                            <li>{{ __('frontend.min_characters') }}</li>
                            <li>{{ __('frontend.atleast_case') }}</li>
                            <li>{{ __('frontend.atleast_digit_special') }}</li>
                        </ul>

                        @error('password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.confirm_password') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative mb-2">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21"
                                    viewBox="0 0 21 21" fill="none">
                                    <path
                                        d="M7.11914 8.52962V6.02962C7.11914 5.14557 7.47033 4.29772 8.09545 3.6726C8.72057 3.04748 9.56842 2.69629 10.4525 2.69629C11.3365 2.69629 12.1844 3.04748 12.8095 3.6726C13.4346 4.29772 13.7858 5.14557 13.7858 6.02962V8.52962"
                                        stroke="#7B7B7B" stroke-width="1.5" stroke-linecap="round" />
                                    <path
                                        d="M4.61914 8.52979H16.2858V16.0298C16.2858 16.4718 16.1102 16.8957 15.7977 17.2083C15.4851 17.5209 15.0612 17.6965 14.6191 17.6965H6.28581C5.84378 17.6965 5.41986 17.5209 5.1073 17.2083C4.79474 16.8957 4.61914 16.4718 4.61914 16.0298V8.52979Z"
                                        stroke="#7B7B7B" stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M12.5352 13.1128H12.5435V13.1211H12.5352V13.1128Z" stroke="#7B7B7B"
                                        stroke-width="2.25" stroke-linejoin="round" />
                                </svg>
                            </div>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5 ps-10"
                                placeholder="{{ __('frontend.enter_confirm_password') }}"   value="{{ old('password_confirmation') }}" autocomplete="new-password"/>

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="toggleConfirmPassword()">
                                <!-- Eye icon -->
                                <svg id="eye-icon-confirm" class="w-5 h-5 text-gray-400 block" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5
                                        c4.478 0 8.268 2.943 9.542 7
                                        -1.274 4.057-5.064 7-9.542 7
                                        -4.477 0-8.268-2.943-9.542-7z" />
                                </svg>

                                <!-- Eye-off icon -->
                                <svg id="eye-off-icon-confirm" class="w-5 h-5 text-gray-400 hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7
                                        a9.957 9.957 0 012.122-3.362M9.88 9.88a3 3 0 104.242 4.242
                                        M7.5 7.5L3 3m18 18l-4.5-4.5M14.121 14.121L17 17" />
                                </svg>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-start mb-5">
                        <div class="flex items-center h-5">
                            <input id="terms" type="checkbox" value=""
                                class="w-4 h-4 border border-gray-300 rounded-sm bg-gray-50 focus:ring-3 focus:ring-blue-300"
                                required />
                        </div>
                        <label for="terms" class="cursor-pointer ms-2 text-sm font-normal text-gray-900">
                            {!! __('frontend.agree_terms', ['terms' => '<a href="#" class="underline text-[#B9A572]">' . __('frontend.terms') . '</a>']) !!}
                        </label>
                    </div>

                    <button type="submit" class="cursor-pointer w-full uppercase px-4 py-3 text-white rounded-lg bg-[#04502E]">
                        {{ __('frontend.sign_up') }}
                    </button>
                </form>

                <p class="text-base text-center text-gray-600">
                    {{ __('frontend.have_an_account') }}
                    <a href="{{ route('frontend.login') }}" class="font-medium">{{ __('frontend.sign_in') }}</a>
                </p>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        function togglePassword() {
            const input = document.getElementById("password");
            const eye = document.getElementById("eye-icon");
            const eyeOff = document.getElementById("eye-off-icon");

            if (input.type === "password") {
                input.type = "text";
                eye.classList.add("hidden");
                eyeOff.classList.remove("hidden");
            } else {
                input.type = "password";
                eye.classList.remove("hidden");
                eyeOff.classList.add("hidden");
            }
        }

        function toggleConfirmPassword() {
            const input = document.getElementById("password_confirmation");
            const eye = document.getElementById("eye-icon-confirm");
            const eyeOff = document.getElementById("eye-off-icon-confirm");

            if (input.type === "password") {
                input.type = "text";
                eye.classList.add("hidden");
                eyeOff.classList.remove("hidden");
            } else {
                input.type = "password";
                eye.classList.remove("hidden");
                eyeOff.classList.add("hidden");
            }
        }
    </script>
@endsection
