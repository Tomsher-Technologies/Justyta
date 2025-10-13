@extends('layouts.web_default', ['title' => __('frontend.request_submit_failed')])

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)]">
            <div class="bg-[#FFFAF0] border !border-[#FFE9B1] rounded-xl p-8 max-w-xl m-auto w-full text-center">

                <div class="w-20 h-20 mx-auto bg-custom-brown rounded-full flex items-center justify-center mb-6">
                    <img src="{{ asset('assets/images/failed.png') }}" alt="{{ __('frontend.failed') }}">
                </div>

                <h1 class="text-2xl font-light text-gray-800 mb-4">{{ __('frontend.request_submit_failed') }}</h1>

                <div class="bg-[#FFF9F4] p-8 rounded-lg mb-6 border border-[#F5E4BA]">
                    <div class="text-gray-700 text-sm mb-3">{!! $pageData['content'] ?? '' !!}</div>
                </div>

                <p class="text-gray-700 text-lg mb-4">{{ __('frontend.thank_you_for_choosing') }}</p>
                <div class="mb-8">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('assets/images/logo-text.svg') }}" alt="{{ __('frontend.logo') }}" class="mx-auto h-12">
                    </a>
                </div>
            </div>

            <div class="text-center mt-6 ">
                <a href="{{ route('user-report-problem') }}" class="underline mr-6">{{ __('frontend.report_a_problem') }}</a>
                <a href="{{ route('user-rate-us') }}" class="underline">{{ __('frontend.rate_us') }}</a>
            </div>

            <div class="text-center">
                <a href="{{ route('user.dashboard') }}"
                    class="mt-8 inline-flex items-center gap-3 px-6 py-3 bg-transparatnt border border-[#4D1717] text-[#4D1717]  rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16"
                        fill="none">
                        <path
                            d="M1.50586 14.9463H5.19786V9.86928C5.19786 9.64061 5.27553 9.44895 5.43086 9.29428C5.58553 9.13895 5.77719 9.06128 6.00586 9.06128H9.00586C9.23453 9.06128 9.42653 9.13895 9.58186 9.29428C9.73653 9.44895 9.81386 9.64061 9.81386 9.86928V14.9463H13.5059V6.25428C13.5059 6.15161 13.4835 6.05828 13.4389 5.97428C13.3942 5.89028 13.3332 5.81695 13.2559 5.75428L7.87186 1.69628C7.76919 1.60695 7.64719 1.56228 7.50586 1.56228C7.36453 1.56228 7.24286 1.60695 7.14086 1.69628L1.75586 5.75428C1.67919 5.81828 1.61819 5.89161 1.57286 5.97428C1.52753 6.05695 1.50519 6.15028 1.50586 6.25428V14.9463ZM0.505859 14.9463V6.25428C0.505859 5.99828 0.563193 5.75595 0.677859 5.52728C0.792526 5.29861 0.950526 5.11028 1.15186 4.96228L6.53686 0.88428C6.81886 0.668946 7.14086 0.561279 7.50286 0.561279C7.86486 0.561279 8.18886 0.668946 8.47486 0.88428L13.8599 4.96128C14.0619 5.10928 14.2199 5.29795 14.3339 5.52728C14.4485 5.75595 14.5059 5.99828 14.5059 6.25428V14.9463C14.5059 15.2143 14.4062 15.4479 14.2069 15.6473C14.0075 15.8466 13.7739 15.9463 13.5059 15.9463H9.62186C9.39253 15.9463 9.20053 15.8689 9.04586 15.7143C8.89119 15.5589 8.81386 15.3669 8.81386 15.1383V10.0623H6.19786V15.1383C6.19786 15.3676 6.12053 15.5596 5.96586 15.7143C5.81119 15.8689 5.61953 15.9463 5.39086 15.9463H1.50586C1.23786 15.9463 1.00419 15.8466 0.804859 15.6473C0.605526 15.4479 0.505859 15.2143 0.505859 14.9463Z"
                            fill="#4D1717" />
                    </svg> 
                    {{ __('frontend.back_to_home') }}
                </a>
            </div>

        </div>

    </div>
@endsection

@section('script')

    {{-- <script>
        function copyReference() {
            const text = document.getElementById("referenceText").innerText;
            navigator.clipboard.writeText(text).then(function () {
                const message = document.getElementById("copyMessage");
                message.classList.remove('hidden');
                setTimeout(() => message.classList.add('hidden'), 2000);
            }).catch(function (err) {
                console.error('Copy failed', err);
            });
        }
    </script> --}}

@endsection
