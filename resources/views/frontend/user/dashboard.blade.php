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

        <div class="bg-white p-4 xl:p-8 rounded-lg text-center">
            <a href="{{ route('news') }}">
                <img src="{{ asset('assets/images/news.png') }}" alt="{{ __('frontend.news') }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                <h3 class="mb-6 text-lg font-semibold">{{ __('frontend.news') }}</h3>
            </a>
        </div>
        
    </div>
@endsection

@section('ads')
  
    @include('components.ad', ['page' => 'lawfirm_services', 'device' => 'web'])
 
@endsection
