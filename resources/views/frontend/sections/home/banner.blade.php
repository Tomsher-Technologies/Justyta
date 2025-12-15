<section class="w-full px-5 md:px-5 lg:px-5 mt-8">
    <img src="{{ asset($section->image) }}" class="w-full" alt="{{ $section->getTranslation('title', $lang) ?? 'Banner' }}">
</section>