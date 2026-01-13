@extends('layouts.web_default', ['title' => __('frontend.rate_us')])

@section('content')
<div class="grid grid-cols-1 gap-6">
    <div class="bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Rate Us</h2>
        <hr class="mb-5" />
        <form id="rate-form" method="POST" action="{{ route('user.rating.submit') }}">
            @csrf
            <div class="mb-6">
                <div id="star-rating" class="flex space-x-1 cursor-pointer mb-3">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg data-value="{{ $i }}" class="w-8 h-8 text-green-700 star" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2
                            9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                        </svg>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating" value="">
                <p class="text-sm text-gray-600">{{ $pageData['content'] }}</p>
            </div>

            <div class="mb-6">
                <label for="comment" class="block mb-2 text-sm font-medium text-gray-900">
                    {{ __('frontend.comment_optional') }}
                </label>
                <textarea id="comment" name="comment" rows="8"
                    class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg mb-1 border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="{{ __('frontend.type_here') }}"></textarea>
                <span class="text-[#717171] text-sm">0/1000</span>
            </div>

            <button type="submit"
                class="uppercase text-white !bg-[#04502E] hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-12 py-3 text-center">
                {{ __('frontend.submit') }}
            </button>
        </form>
    </div>
</div>
@endsection

@section('ads')
    @php
        $ads = getActiveAd('rate_us', 'web');
    @endphp

    @if ($ads && $ads->files->isNotEmpty())

        <div class="relative w-full mb-12 px-[50px]">
            @php
                $file = $ads->files->first();
            @endphp

            <a href="{{ $ads->cta_url ?? '#' }}" target="_blank" title="{{ $ads->cta_text ?? 'View More' }}">
                @if($file->file_type === 'video')
                    <video id="adVideo{{ $ads->id }}" class="w-full object-cover"  style="height: 500px;" autoplay muted loop playsinline>
                        <source src="{{ asset($file->file_path) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <img src="{{ asset($file->file_path) }}" class="w-full h-80 object-cover" alt="Ad Image">
                @endif
            </a>

            @if($file->file_type === 'video')
                <button 
                    onclick="toggleMute('adVideo{{ $ads->id }}', this)" 
                    class="absolute bottom-2 bg-gray-800 bg-opacity-50 text-white px-3 py-1 rounded hover:bg-opacity-80 z-10" style="right: 4rem;">
                    🔇
                </button>
            @endif
        </div>
    @endif
@endsection

@section('script')

<script>
    document.querySelectorAll('#star-rating .star').forEach(star => {
        star.addEventListener('click', function () {
            const value = this.getAttribute('data-value');
            document.getElementById('rating').value = value;

            document.querySelectorAll('#star-rating .star').forEach(s => {
                s.setAttribute('fill', 'none');
                s.setAttribute('stroke', 'currentColor');
            });

            for (let i = 1; i <= value; i++) {
                const starToFill = document.querySelector(`#star-rating .star[data-value="${i}"]`);
                if (starToFill) {
                    starToFill.setAttribute('fill', 'currentColor');
                    starToFill.removeAttribute('stroke');
                }
            }
        });
    });


    $(document).ready(function () {

        $("#rate-form").validate({
            ignore: [],
            rules: {
                rating: { required: true }
            },
            messages: {
                rating: "{{ __('messages.rating_required') }}",
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('text-red-500 text-sm');

                if (element.attr('name') === 'rating') {
                    $('#star-rating').after(error);
                } else if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2').find('.select2-selection')
                        .addClass('border-red-500');
                } else {
                    $(element).addClass('border-red-500');
                }
            },
            unhighlight: function (element) {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2').find('.select2-selection')
                        .removeClass('border-red-500');
                } else {
                    $(element).removeClass('border-red-500');
                }
            },
            submitHandler: function (form) {
                form.submit(); 
            }
        });

        
    });
</script>

@endsection