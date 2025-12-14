    <section class="bg-[#fff] px-5 md:px-5 lg:px-5 py-[80px] relative">
        <div class="bg-[#FFF9F4] p-4 xl:p-12 h-full ">
            <div class="w-[100%] xl:w-[60%]">
                <h4 class="text-[24px] font-medium mb-3"> {!! $section->getTranslation('subtitle', $lang) !!}</h4>
                <h3 class="text-[20px] xl:text-[40px] font-cinzel font-bold leading-[25px] xl:leading-[55px] mb-5">
                    {!! $section->getTranslation('title', $lang) !!}</h3>
                <p class="text-[16px] font-medium">If Procedures moves slow. You don’t have to.</p>
                <br>
                <!--<p class="text-[16px] font-medium">At Lawpoint, we are committed to upholding justice and protecting-->
                <!--    your rights. With a strong foundation in legal research, advocacy, and client-focused solutions, we-->
                <!--    provide expert legal services tailored to your needs. </p>-->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-12 mt-8 xl:w-max">
                    {!! $section->getTranslation('description', $lang) !!}
                </div>
            </div>
        </div>
        <img src="{{ asset('assets/images/law-img.png') }}" class="absolute end-0 hidden xl:block bottom-0  h-auto"
            alt="">
    </section>
