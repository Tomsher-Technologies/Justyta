@extends('layouts.web_login', ['title' => 'Forgot Password'])

@section('content')
    <section class="bg-[#FFF7F0] px-[100px] py-[80px] pt-0">
        <div class="flex items-center justify-center">
            <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg">
                <h2 class="text-3xl font-semibold text-gray-900 mb-2">
                    {{ __('frontend.reset_password') }}
                </h2>

                @php
                    $lang = app()->getLocale() ?? 'en';
                    $contentDynamic = getPageDynamicContent('reset_password', $lang);
                @endphp
                <p class="text-xs text-gray-600">
                    {{ $contentDynamic['content'] ?? '' }}
                </p>

                <form class="space-y-6" method="POST" action="{{ route('frontend.reset-password') }}">
                    @csrf
                    <div>
                        <label for="email" class="block text-base font-medium text-[#555555] mb-2">{{ __('frontend.email') }}<span
                                class="text-red-500">*</span></label>
                        <div class="relative">
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
                                placeholder="{{ __('frontend.enter_email') }}" value="{{ old('email') }}"/>
                        </div>
                        @error('email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="cursor-pointer w-full px-4 py-3 text-white rounded-lg bg-[#04502E]">
                        {{ __('frontend.submit') }}
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script></script>
@endsection
