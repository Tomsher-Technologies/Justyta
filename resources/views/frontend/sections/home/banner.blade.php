<section class="w-full px-10 md:px-10 lg:px-10 mt-8">
    <img src="{{ asset($section->image) }}" class="w-full" alt="{{ $section->getTranslation('title', $lang) ?? 'Banner' }}">
</section>