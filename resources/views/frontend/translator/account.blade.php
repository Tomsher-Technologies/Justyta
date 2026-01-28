@extends('layouts.web_default', ['title' => __('frontend.my_account')])

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <div class=" bg-white p-4 xl:p-10 rounded-[20px] border !border-[#FFE9B1] ">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('frontend.edit_profile') }}</h2>
            <hr class="mb-5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">



                <form method="POST" action="{{ route('translator.update.profile') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mb-6">
                        <!-- Full Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.full_name') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name', $user->name) }}"
                                class="bg-[#F9F9F9] border @error('full_name') border-red-500 @else border-gray-300 @enderror text-sm rounded-[10px] block w-full p-3.5"
                                required placeholder="{{ __('frontend.enter') }}">
                            @error('full_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.email') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="bg-[#F9F9F9] border border-gray-300 text-gray-400 text-sm rounded-[10px] block w-full p-3.5 cursor-not-allowed">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.phone') }}</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                class="bg-[#F9F9F9] border @error('phone') border-red-500 @else border-gray-300 @enderror text-sm rounded-[10px] block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">
                            @error('phone')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Address -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.address') }}</label>
                            <textarea name="address" rows="5"
                                class="bg-[#F9F9F9] border @error('address') border-red-500 @else border-gray-300 @enderror text-sm rounded-[10px] block w-full p-3.5"
                                placeholder="{{ __('frontend.enter') }}">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Language -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.language') }}</label>
                            <select name="language"
                                class="bg-gray-50 border border-gray-300 text-sm rounded-lg block w-full p-3.5">
                                @php
                                    $langs = [
                                        'en' => 'EN',
                                        'ar' => 'AR',
                                        'fa' => 'FA',
                                        'fr' => 'FR',
                                        'ru' => 'RU',
                                        'zh' => 'ZH',
                                    ];
                                @endphp
                                @foreach ($langs as $key => $label)
                                    <option value="{{ $key }}" @selected(old('language', $user->language) == $key)>{{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('language')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 mb-6">
                        <button type="submit"
                            class="text-white bg-[#04502E] hover:bg-[#02331D] rounded-xl text-md w-full px-8 py-4 text-center">
                            {{ __('frontend.save_changes') }}
                        </button>
                    </div>
                </form>

                <hr class="my-8 mb-5">

                <div class="flex items-center justify-between mt-6">
                    <a href="{{ route('translator.change-password') }}"
                        class="bg-[#E6EDEA] text-[#07683B] p-3 px-6 rounded-lg">
                        {{ __('frontend.change_password') }}
                    </a>

                    <a href="#" id="deleteAccountBtn" class="text-[#4D1717] underline">
                        {{ __('frontend.delete_account') }}
                    </a>
                </div>

            </div>

        </div>

    </div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('account_settings', 'web');
    @endphp

    @if ($ads && $ads->files->isNotEmpty())
        <div class="relative w-full mb-12 px-[50px]">
            @php
                $file = $ads->files->first();
            @endphp

            <a href="{{ $ads->cta_url ?? '#' }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
                @if($file->file_type === 'video')
                    <video id="adVideo{{ $ads->id }}" class="w-full object-cover"  style="height: 500px;" autoplay muted loop playsinline>
                        <source src="{{ asset($file->file_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ asset($file->file_path) }}" class="w-full h-80 object-cover" alt="Ad Image">
                @endif
            </a>

            @if($file->file_type === 'video')
                <button 
                    onclick="toggleMute('adVideo{{ $ads->id }}', this)" 
                    class="absolute bottom-2 bg-gray-800 bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-80 z-10" style="right: 4rem;">
                    <!-- Unmute Icon -->
                    <svg id="unmuteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-600 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9v6h4l5 5V4l-5 5H9" />
                    </svg>

                    <!-- Mute Icon -->
                    <svg id="muteIcon" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white-500 " fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9v6h4l5 5V4l-5 5H9" />
                        <line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </button>
            @endif
        </div>
    @endif
@endsection

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.min.css') }}">
@endsection

@section('script')
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <script>
       
    </script>
@endsection
