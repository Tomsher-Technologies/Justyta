@extends('layouts.web_login', ['title' => $news->translate($lang)->title ?? 'News Details'])

@section('content')

@php
$translation = $news->translate($lang);
@endphp

<section class="py-[40px] px-5 md:px-5 lg:px-5 my-14 relative overflow-hidden flex-grow">
    <div class="container m-auto">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            @if($news->image)
            <img src="{{ asset($news->image) }}" class="w-full h-[400px] object-cover rounded-lg mb-6" alt="{{ $translation->title ?? '' }}">
            @endif

            <div class="flex items-center gap-4 mb-4 text-sm text-gray-500">
                <span>{{ date('M d, Y', strtotime($news->news_date)) }}</span>
            </div>

            <h1 class="text-3xl font-bold text-[#034833] mb-6 font-cinzel">{{ $translation->title ?? '' }}</h1>

            <div class="prose max-w-none text-[#4B535D] content-article" >
                {!! $translation->description ?? '' !!}
            </div>

            <!-- <div class="mt-8 border-t pt-6">
                <h3 class="text-xl font-bold text-[#034833] font-cinzel mb-4">{{ __('Share this news') }}</h3>
                <div class="flex gap-4">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="text-[#3b5998] hover:text-[#2d4373]">
                        <i class="fab fa-facebook fa-2x"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $translation->title ?? '' }}" target="_blank" class="text-[#1da1f2] hover:text-[#0c85d0]">
                        <i class="fab fa-twitter fa-2x"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}&title={{ $translation->title ?? '' }}" target="_blank" class="text-[#0077b5] hover:text-[#005885]">
                        <i class="fab fa-linkedin fa-2x"></i>
                    </a>
                </div>
            </div> -->
        </div>
    </div>
</section>

@endsection