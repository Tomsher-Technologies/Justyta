@extends('layouts.web_default', ['title' => 'User Dashboard'])

@section('content')
    <!-- Consultancy Form -->
    <h2 class="text-xl font-medium text-gray-800 mb-4">
        Law Firm Services
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
            <div class="bg-white p-8 rounded-lg text-center">
                <img src="{{ asset(getUploadedImage($serv->icon)) }}" alt="{{ $translation?->title }}" class="mb-4 w-20 h-20 mx-auto object-contain" />
                <h3 class="mb-6 text-lg font-semibold">{{ $translation?->title }}</h3>
            </div>
        @empty
            
        @endforelse
       
       
        
    </div>
@endsection

@section('ads')
    <div class="w-full mb-12 px-[50px]">
        <img src="{{ asset('assets/images/ad-img.jpg') }}" class="w-full" alt="" />
    </div>
@endsection
