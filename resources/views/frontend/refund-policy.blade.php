@extends('layouts.web_login', ['title' => 'Refund Policy'])

@section('content')    

    <section class="py-[40px] md:py-[0px] px-0 md:px-10 lg:px-20 xl:px-0">
      <div class="container m-auto px-5 md:px-20">
         <!-- Title -->
         @php
            $pageData = $page->sections->first();
         @endphp
         <h1 class="mb-5 text-2xl md:text-5xl text-[#034833] font-cinzel text-center">{{ $pageData?->getTranslation('title', $lang) }}</h1>
         <!-- Meta -->
        
         <!-- Blog Content -->
         <article class="prose prose-lg max-w-none content-article">
            {!! $pageData?->getTranslation('description', $lang) !!}
         </article>
      </div>
   </section>

@endsection