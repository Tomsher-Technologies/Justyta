    <section class="bg-[#FFF] px-10 md:px-10 lg:px-10 py-[80px]">
        <div class="grid grid-cols-1 xl:grid-cols-2 items-center">
            <div>
                <h3 class="xl:text-[40px] text-[28px] leading-[32px]  font-cinzel font-bold xl:leading-[55px] mb-5"> {!! $section->getTranslation('title', $lang) !!}</h3>
                <p class="text-[16px] font-medium">{!! $section->getTranslation('description', $lang) !!}</p>
                <div class="grid grid-cols-2 gap-3 w-max mt-6">
                    <a href="{{ $section->link1  ?? '#'}}" target="_blank">
                        <img src="{{ asset($section->image1) }}" alt="">
                    </a>
                    <a href="{{ $section->link2  ?? '#'}}" target="_blank">
                        <img src="{{ asset($section->image2) }}" alt="">
                    </a>
                </div>
            </div>
            <img src="{{ asset($section->image) }}" alt="">
        </div>
    </section>