@extends('layouts.web_login', ['title' => 'Login In'])

@section('content')
    <section class="bg-[#FFF7F0] px-[100px] py-[80px] pt-0">
        <div class="flex items-center justify-center">
            <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg">
                <h2 class="text-3xl font-semibold text-gray-900 mb-8">{{ __('frontend.sign_in') }}</h2>

                @php
                    $lang = app()->getLocale() ?? 'en';
                    $contentDynamic = getPageDynamicContent('login_page', $lang);
                @endphp
                <p class="text-xs text-gray-600">
                    {{ $contentDynamic['content'] ?? '' }}
                </p>

                <p class="text-base text-gray-600">
                    
                    {{ __('frontend.dont_have_an_account') }}
                    <a href="{{ route('frontend.register') }}" class="font-medium">{{ __('frontend.sign_up') }}</a>
                </p>

                <form class="space-y-6" method="POST" action="{{ route('frontend.login.submit') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-base font-medium text-[#555555] mb-2">{{ __('frontend.email') }}<span
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
                                placeholder="{{ __('frontend.enter_email') }}" />
                            
                        </div>
                        @error('email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-base font-medium text-[#555555] mb-2">{{ __('frontend.password') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative mb-2">
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
                                placeholder="{{ __('frontend.enter_password') }}" />
                            
                            <!-- Toggle Button -->
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
                        @error('password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="cursor-pointer w-full px-4 py-3 uppercase text-white rounded-lg bg-[#04502E]">
                        {{ __('frontend.sign_in') }}
                    </button>
                </form>

                <div class="text-sm text-right">
                    <a href="{{ route('frontend.forgot-password') }}" class="font-medium text-[#555555]">{{ __('frontend.forgot_password') }}</a>
                </div>
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
</script>
@endsection
