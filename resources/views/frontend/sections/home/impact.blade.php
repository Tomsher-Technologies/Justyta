    <section class="bg-[#fff] px-10 md:px-10 lg:px-10 py-[80px] relative">
        <div class="bg-[#FFF9F4] p-4 xl:p-12 h-full ">
            <div class="w-[100%] xl:w-[60%]">
                <h4 class="text-[24px] font-medium mb-3"> {!! $section->getTranslation('subtitle', $lang) !!}</h4>
                <h3 class="text-[20px] xl:text-[40px] font-cinzel font-bold leading-[25px] xl:leading-[55px] mb-5">
                    {!! $section->getTranslation('title', $lang) !!}</h3>
                <p class="text-[16px] font-medium"> {!! $section->getTranslation('description', $lang) !!}</p>
                <br>

                @php
                $items = $section->getTranslation('content', $lang);
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-12 mt-8 xl:w-max">
                    @if(!empty($items) && is_array($items) && count($items) > 0)
                    @foreach($items as $item)
                    <div class="border-b !border-[#C9C5C1] flex flex-col xl:flex-row items-center gap-5 pb-6">
                        <h3 class="font-cinzel text-[30px] font-bold text-[#B9A572]"> {!! $item['title'] !!}</h3>
                        <h5 class="text-[#000] text-[15px] font-medium"> {!! $item['description'] !!}</h5>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
        <img src="{{ asset($section->image) }}" class="absolute end-0 hidden xl:block bottom-0  h-auto"
            alt="">
    </section>