@extends('layouts.web_login', ['title' => 'Services'])

@section('content')
    <section class="py-[40px] px-10 md:px-10 lg:px-10 my-14 relative overflow-hidden flex-grow">

        @php
            $pageData = $page->sections->first();
        @endphp
        <div class="container m-auto">
            <h4 class="mb-3 text-[30px] text-[#034833] font-cinzel">{{ $pageData?->getTranslation('title', $lang) }}</h4>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($news as $item)
                    @php
                        $translation = $item->translate($lang);
                    @endphp
                    <div>
                        <a href="{{ route('news.details', $item->id) }}">
                            <img src="{{ asset($item->image) }}" class="mb-3 w-full h-[200px] object-cover"
                                alt="{{ $translation->title ?? '' }}">
                            <span>{{ date('M d, Y', strtotime($item->news_date)) }}</span>
                            <h3 class="my-1 text-[#121C27] font-medium text-[20px] line-clamp-2">
                                {{ $translation->title ?? '' }}</h3>
                            <p class="text-[#4B535D] text-[16px] line-clamp-3">
                                {{ Str::limit(strip_tags($translation->description ?? ''), 100) }}</p>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-center">
                {{ $news->links() }}
            </div>
        </div>




    </section>
@endsection
