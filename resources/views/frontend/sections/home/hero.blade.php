<section class="container m-auto flex-wrap xl:flex-col flex justify-between items-start pt-[50px] xl:pb-6 xl:mt-[-150px] px-5">
    <div class="grid grid-cols-1 xl:grid-cols-[17.5%_1fr_20%] w-full items-center gap-6">

        <div class="hidden xl:block"></div>

        <div class="w-full">
            @if($section->getTranslation('title', $lang))
                <h3 class="text-[22px] md:text-[50px] xl:text-[60px] xl:leading-[60px] font-cinzel font-normal text-[#07683B]">
                    {!! $section->getTranslation('title', $lang) !!}
                </h3>
            @endif
        </div>

        @if($section->getTranslation('button_text', $lang))
            <a href="{{ $section->getTranslation('button_link', $lang) ?? '#' }}"
               class="flex items-center mt-3 justify-between px-6 pe-4 py-3 bg-[#07683B] text-white rounded-full shadow-lg hover:bg-green-800">
                <span class="text-base xl:text-lg font-medium mr-4">
                    {{ $section->getTranslation('button_text', $lang) }}
                </span>
                <span class="flex items-center justify-center w-10 h-10 bg-white text-green-700 rounded-full">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>
        @endif

    </div>
</section>
