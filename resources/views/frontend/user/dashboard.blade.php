@extends('layouts.web_default', ['title' => 'User Dashboard'])

@section('content')
    <!-- Consultancy Form -->
    <h2 class="text-xl font-medium text-gray-800 mb-4">
        {{ __('frontend.law_firm_services') }}
    </h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-5 gap-2">

        {{-- <div class="bg-white p-4 xl:p-8 rounded-lg text-center">
            <img src="{{ $imageUrl }}" alt="Last Will Icon" class="mb-4 w-20 h-20 mx-auto object-contain" />
            <h3 class="mb-6 text-lg font-semibold">Last Will & Testament</h3>
        </div> --}}
        @forelse ($services as $serv)
            @php
                $translation = $serv->translations->first();
            @endphp
            @if($serv->slug === 'online-live-consultancy')
                <div class="bg-white p-4 xl:p-8 rounded-lg text-center">
                    <a href="{{ route('service.online.consultation') }}">
                        <img src="{{ asset(getUploadedImage($serv->icon)) }}" alt="{{ $translation?->title }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                        <h3 class="mb-6 text-lg font-semibold">{{ $translation?->title }}</h3>
                    </a>
                </div>
            @else
                <div class="bg-white p-4 xl:p-8 rounded-lg text-center">
                    <a href="{{ route('service.request.form',['slug' => $serv->slug]) }}">
                        <img src="{{ asset(getUploadedImage($serv->icon)) }}" alt="{{ $translation?->title }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                        <h3 class="mb-6 text-lg font-semibold">{{ $translation?->title }}</h3>
                    </a>
                </div>
            @endif
        @empty
            
        @endforelse
       
        <div class="bg-white p-4 xl:p-8 rounded-lg text-center">
            <a href="{{ route('user-training-request') }}">
                <img src="{{ asset('assets/images/training_request.png') }}" alt="{{ __('frontend.law_training_request') }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                <h3 class="mb-6 text-lg font-semibold">{{ __('frontend.law_training_request') }}</h3>
            </a>
        </div>

        <div class="bg-white p-4 xl:p-8 rounded-lg text-center">
            <a href="{{ route('user-lawfirm-jobs') }}">
                <img src="{{ asset('assets/images/jobs.png') }}" alt="{{ __('frontend.law_firm_jobs') }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                <h3 class="mb-6 text-lg font-semibold">{{ __('frontend.law_firm_jobs') }}</h3>
            </a>
        </div>
        
    </div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('lawfirm_services', 'web');
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
                    class="absolute bottom-2 bg-gray-800 bg-opacity-50 text-white px-2 py-1 rounded hover:bg-opacity-80 z-10" style="right: 4rem;">
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
