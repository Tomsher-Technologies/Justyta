@extends('layouts.web_login', ['title' => 'About Us'])

@section('content') 

@if($page && $page->sections->count() > 0)
<div class="py-[30px] md:py-[80px]">

    @foreach($page->sections as $section)
        @if($section->status == 1)
            @php
                $sectionType = $section->section_type;
            @endphp
            
            @if($sectionType == 'about_main' || $sectionType == 'custom')
                <section class="px-5 md:px-5 lg:px-5 xl:px-0">
                    <div class="container m-auto pb-0">
                        <div class="flex justify-between flex-col xl:flex-row gap-[50px]">
                            <div>
                                @if($section->getTranslation('subtitle', $lang))
                                    <h4 class="text-[24px] font-medium text-[#07683B]">{{ $section->getTranslation('subtitle', $lang) }}</h4>
                                @endif
                                
                                @if($section->getTranslation('title', $lang))
                                    <h3 class="text-[26px] leading-[35px] md:text-[40px] font-cinzel font-bold md:leading-[55px] mb-5 text-[#034833]">
                                 {!! $section->getTranslation('title', $lang) !!}
                                    </h3>
                                @endif
                                
                               @if($section->getTranslation('description', $lang))
                                    <div class="text-[16px] font-medium prose max-w-none">
                                        {!! $section->getTranslation('description', $lang) !!}
                                    </div>
                                @endif
                                
                                @if($section->getTranslation('button_text', $lang))
                                    <div class="mt-6">
                                        <a href="{{ $section->getTranslation('button_link', $lang) ?? '#' }}" 
                                           class="flex items-center justify-between px-6 pe-4 py-3 bg-[#07683B] text-white rounded-full shadow-lg hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-opacity-50 w-max">
                                            <span class="text-lg font-medium mr-4">{{ $section->getTranslation('button_text', $lang) }}</span>
                                            <div class="flex items-center justify-center w-10 h-10 bg-white text-green-700 rounded-full">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                </svg>
                                            </div>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @endif
            
            @if($sectionType == 'hero')
                <section class="w-full px-5 md:px-5 lg:px-5 mt-8">
                    @if($section->image)
                        <img src="{{ asset($section->image) }}" class="w-full" alt="{{ $section->getTranslation('title', $lang) ?? 'Banner' }}">
                    @endif
                    @if($section->getTranslation('title', $lang))
                        <div class="container m-auto mt-8">
                            <h2 class="text-[30px] md:text-[40px] font-cinzel font-bold text-[#034833]">
                                {!! $section->getTranslation('title', $lang) !!}
                            </h2>
                        </div>
                    @endif
                </section>
            @endif
            
            @if($sectionType == 'features')
                <section class="py-[20px] md:py-[20px] px-5 md:px-5 lg:px-5 xl:px-0">
                    <div class="container m-auto">
                        @if($section->getTranslation('title', $lang))
                            <h3 class="text-[30px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">
                                {{ $section->getTranslation('title', $lang) }}
                            </h3>
                        @endif
                        
                        @if($section->getTranslation('description', $lang))
                            <div class="text-[16px] font-medium prose max-w-none">
                                {!! $section->getTranslation('description', $lang) !!}
                            </div>
                        @endif
                    </div>
                </section>
            @endif
        @endif
    @endforeach
@else
    <section class="py-[30px] md:py-[80px] px-5 md:px-5 lg:px-5 xl:px-0">
        <div class="container m-auto text-center">
            <h3 class="text-[30px] font-cinzel font-bold mb-5 text-[#034833]">About Us</h3>
        </div>
    </section>
@endif
</div>

@endsection


@section('style')
<style>
.prose ul li {
    position: relative;
    padding-left: 2rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.prose ul li::before {
    content: "";
    width: 1.5rem;
    height: 1.5rem;
    min-width: 1.5rem;
    background-repeat: no-repeat;
    background-size: contain;
    background-position: center;
    margin-top: 2px;
    flex-shrink: 0;

    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23B9A572' viewBox='0 0 24 24'%3E%3Cpath fill-rule='evenodd' d='M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z' clip-rule='evenodd'/%3E%3C/svg%3E");
}
</style>
@endsection
