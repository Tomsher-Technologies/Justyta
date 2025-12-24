    <section class="bg-[#FFF] px-5 md:px-5 lg:px-5 py-[80px]">
        <div class="grid grid-cols-1 xl:grid-cols-2 items-center">
            <div>
                <h3 class="xl:text-[40px] text-[28px] leading-[32px]  font-cinzel font-bold xl:leading-[55px] mb-5"> {!! $section->getTranslation('title', $lang) !!}</h3>
                <p class="text-[16px] font-medium">{!! $section->getTranslation('description', $lang) !!}</p>
                <div class="grid grid-cols-2 gap-3 w-max mt-6">
                    <a href="/">
                        <img src="{{ asset('assets/images/play-store.svg') }}" alt="">
                    </a>
                    <a href="/">
                        <img src="{{ asset('assets/images/app-store.svg') }}" alt="">
                    </a>
                </div>
            </div>
            <img src="{{ asset($section->image) }}" alt="">
        </div>
    </section>