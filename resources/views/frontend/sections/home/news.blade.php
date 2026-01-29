    <section class="bg-[#FFF7F0] px-5 md:px-5 lg:px-5 py-[80px]">
        <h4 class="mb-3 text-[30px] text-[#034833] font-cinzel">{!! $section->getTranslation('title', $lang) !!}</h4>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @foreach ($news as $item)
                @php
                    $translation = $item->translate($lang);
                @endphp
                <div>
                    <a href="{{ route('news.details', $item->id) }}">
                        <img src="{{ asset($item->image) }}" class="mb-3 w-full h-[200px] object-cover"
                            alt="{{ $translation->title ?? '' }}">
                        <span class="text-[#121C27] text-[15px]">{{ date('M d, Y', strtotime($item->news_date)) }}</span>
                        <h3 class="my-1 text-[#121C27] font-medium text-[20px] line-clamp-2">
                            {{ $translation->title ?? '' }}</h3>
                        <p class="text-[#4B535D] text-[16px] line-clamp-3">
                            {{ Str::limit(strip_tags($translation->description ?? ''), 100) }}</p>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('news') }}"
                class="inline-block px-6 py-2 text-white bg-[#034833] rounded hover:bg-[#023324] transition">{!! $section->getTranslation('button_text', $lang) !!}</a>
        </div>
    </section>
