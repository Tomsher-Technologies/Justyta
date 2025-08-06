@extends('layouts.web_login', ['title' => 'OTP Password'])

@section('content')
    <section class="bg-[#FFF7F0] px-[100px] py-[80px] pt-0">
        <div class="flex items-center justify-center">
            <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg">
                <h2 class="text-3xl font-semibold text-gray-900 mb-4">{{ __('frontend.enter_otp') }}</h2>
                
                @php
                    $lang = app()->getLocale() ?? 'en';
                    $contentDynamic = getPageDynamicContent('enter_otp', $lang);
                @endphp
                <p class="text-xs text-gray-600">
                    {{ $contentDynamic['content'] ?? '' }}
                </p>
            
                <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6" id="otp-form">
                    @csrf

                    <div class="grid grid-cols-4 gap-4 text-center">
                        @for ($i = 1; $i <= 4; $i++)
                            <input type="text"
                                name="digit{{ $i }}"
                                maxlength="1"
                                class="otp-input bg-[#F9F9F9] border border-gray-300 text-gray-900 text-xl font-bold text-center rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                                >
                        @endfor
                    </div>

                    {{-- Hidden full OTP field --}}
                    <input type="hidden" name="otp" id="otp-full">

                    <button type="submit" class="cursor-pointer w-full px-4 py-3 text-white rounded-lg bg-[#04502E]">
                        {{ __('frontend.submit') }}
                    </button>
                </form>
                <div class="mt-4 text-sm">
                    <span id="countdown-text" class="text-gray-600">{{ __('frontend.resend_code_in') }} <span id="countdown">35</span>s</span>
                    <div id="resend-link" class=" hidden">{{ __('frontend.didnt_receive_otp') }} <a href="{{ route('otp.resend') }}" class="float-right underline text-[#0a0aba]">{{ __('frontend.resend_otp') }}</a></div>
                </div>
                @if(session('error'))
                    <div class="text-red-600 mb-4 mt-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.otp-input');
            const hiddenOtpInput = document.getElementById('otp-full');
            const form = document.getElementById('otp-form');

            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value;
                    if (/^\d$/.test(value) && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });

            form.addEventListener('submit', function (e) {
                let otp = '';
                inputs.forEach(input => {
                    otp += input.value;
                });

                if (otp.length !== 4) {
                    e.preventDefault();
                    toastr.error("{{ __('frontend.enter_all_digits') }}");
                    return;
                }

                hiddenOtpInput.value = otp;
            });

        });
        document.addEventListener('DOMContentLoaded', function () {
            let countdown = 40;
            const countdownEl = document.getElementById('countdown');
            const countdownText = document.getElementById('countdown-text');
            const resendLink = document.getElementById('resend-link');

            const interval = setInterval(() => {
                countdown--;
                countdownEl.textContent = countdown;

                if (countdown <= 0) {
                    clearInterval(interval);
                    countdownText.classList.add('hidden');
                    resendLink.classList.remove('hidden');
                }
            }, 1000);
        });
    </script>
@endsection
