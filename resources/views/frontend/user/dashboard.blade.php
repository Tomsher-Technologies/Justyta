@extends('layouts.web_default', ['title' => 'User Dashboard'])

@section('content')
    <!-- Consultancy Form -->
    <h2 class="text-xl font-medium text-gray-800 mb-4">
        {{ __('frontend.law_firm_services') }}
    </h2>
    <div class="grid grid-cols-5 gap-2">

        {{-- <div class="bg-white p-8 rounded-lg text-center">
            <img src="{{ $imageUrl }}" alt="Last Will Icon" class="mb-4 w-20 h-20 mx-auto object-contain" />
            <h3 class="mb-6 text-lg font-semibold">Last Will & Testament</h3>
        </div> --}}
        @forelse ($services as $serv)
            @php
                $translation = $serv->translations->first();
            @endphp
            @if($serv->slug === 'online-live-consultancy')
                <div class="bg-white p-8 rounded-lg text-center">
                    <a href="{{ route('service.online.consultation') }}">
                        <img src="{{ asset(getUploadedImage($serv->icon)) }}" alt="{{ $translation?->title }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                        <h3 class="mb-6 text-lg font-semibold">{{ $translation?->title }}</h3>
                    </a>
                </div>
            @else
                <div class="bg-white p-8 rounded-lg text-center">
                    <a href="{{ route('service.request.form',['slug' => $serv->slug]) }}">
                        <img src="{{ asset(getUploadedImage($serv->icon)) }}" alt="{{ $translation?->title }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                        <h3 class="mb-6 text-lg font-semibold">{{ $translation?->title }}</h3>
                    </a>
                </div>
            @endif
        @empty
            
        @endforelse
       
        <div class="bg-white p-8 rounded-lg text-center">
            <a href="{{ route('user-training-request') }}">
                <img src="{{ asset('assets/images/training_request.png') }}" alt="{{ __('frontend.law_training_request') }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                <h3 class="mb-6 text-lg font-semibold">{{ __('frontend.law_training_request') }}</h3>
            </a>
        </div>

        <div class="bg-white p-8 rounded-lg text-center">
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
