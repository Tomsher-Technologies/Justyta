    <section class="bg-[#FFF7F0] px-10 md:px-10 lg:px-10 py-[80px]">
        <div class="grid grid-cols-1 xl:grid-cols-2 w-[100%] xl:w-[80%] m-auto items-center gap-6">
            <img src="{{ asset($section->image) }}" alt="">
            <div>
                <h4 class="text-[24px] font-medium mb-3">{!! $section->getTranslation('subtitle', $lang) !!}</h4>
                <h3 class="text-[28px] xl:text-[40px] font-cinzel font-bold leading-[32px] xl:leading-[55px] mb-5"> {!! $section->getTranslation('title', $lang) !!}</h3>
                <p class="text-[16px] font-medium">{!! $section->getTranslation('description', $lang) !!}</p>
                @php
                $items = $section->getTranslation('content', $lang);
                @endphp
                <ul class="mt-6 grid grid-cols-2 gap-3 mb-8">
                    @if(!empty($items) && is_array($items) && count($items) > 0)
                    @foreach($items as $item)
                    <li class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd"
                                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>{{ $item['title'] ?? '' }}</span>
                    </li>
                    @endforeach
                    @endif
                </ul>
                <button
                    class="flex items-center justify-between px-6 pe-4 py-3 bg-[#07683B] text-white rounded-full shadow-lg hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-opacity-50">
                    <span class="xl:text-lg text-base font-medium mr-4"><a href="{{$section->getTranslation('button_link', $lang)}}">{{$section->getTranslation('button_text', $lang)}}</a></span>
                    <div class="flex items-center justify-center w-10 h-10 bg-white text-green-700 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </section>