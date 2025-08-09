@extends('layouts.web_default', ['title' => __('frontend.change') ])

@section('content')
<div class="flex justify-center items-center py-24 bg-gray-50"> 
    <div class="w-full max-w-xl">
        <div class="bg-white p-10 rounded-[20px] border !border-[#FFE9B1] shadow">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4 text-center">{{ __('frontend.change_password') }}</h2>
                <hr class="mb-6">
            <form id="change-password-form" method="POST" action="{{ route('user.update-new-password') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('frontend.current_password') }}<span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password"
                            class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring focus:border-blue-500 pr-10">
                        <span onclick="togglePassword('current_password', this)" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 show-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>
                    @error('current_password') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>


                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('frontend.new_password') }}<span class="text-red-500">*</span>
                    </label>
                
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password" class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring focus:border-blue-500">
                        <span onclick="togglePassword('new_password', this)" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 show-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>
                    @error('new_password') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('frontend.confirm_password') }}<span class="text-red-500">*</span>
                    </label>
                    
                    <div class="relative">
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="w-full border border-gray-300 p-3 rounded-md focus:outline-none focus:ring focus:border-blue-500" >
                        <span onclick="togglePassword('new_password_confirmation', this)" class="absolute inset-y-0 right-3 flex items-center cursor-pointer text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 show-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </div>

                    @error('new_password_confirmation') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full bg-[#04502E] hover:bg-[#02331D] text-white py-3 px-6 rounded-md transition duration-200 uppercase">
                        {{ __('frontend.change_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('change_password', 'web');
    @endphp

    @if ($ads && $ads->files->isNotEmpty())

        <div class="w-full mb-12 px-[50px]">
            {{-- <img src="{{ asset('assets/images/ad-img.jpg') }}" class="w-full" alt="" /> --}}
           {{-- muted --}}
            @php
                $file = $ads->files->first();
                $media = $file->file_type === 'video'
                    ? '<video class="w-full h-100" autoplay loop>
                        <source src="' . asset($file->file_path) . '" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>'
                    : '<img src="' . asset($file->file_path) . '" class="w-full h-80" alt="Ad Image">';
            @endphp

            @if (!empty($ads->cta_url))
                <a href="{{ $ads->cta_url }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
                    {!! $media !!}
                </a>
            @else
                {!! $media !!}
            @endif
        </div>
    @endif
@endsection

@section('script')
<script>
    function togglePassword(fieldId, iconSpan) {
        const input = document.getElementById(fieldId);
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        iconSpan.innerHTML = isPassword
            ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hide-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.052 10.052 0 011.659-2.882M9.88 9.88a3 3 0 014.242 4.242M15 12a3 3 0 00-3-3"/>
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M3 3l18 18" />
               </svg>`
            : `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 show-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
               </svg>`;
    }
</script>

@endsection 

