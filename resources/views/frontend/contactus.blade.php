@extends('layouts.web_login', ['title' => 'Contact Us'])

@section('content')
    <section class="py-[40px] my-14 min-h-screen px-10 md:px-10 lg:px-10">

        @php
            $pageData = $page->sections->first();
        @endphp

        <div class="container m-auto px-5">
            <h4 class="mb-3 text-[30px] text-[#034833] font-cinzel">{{ $pageData?->getTranslation('title', $lang) }}</h4>
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                <div>

                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="mb-5">
                            <label for="name"
                                class="block mb-2 text-sm font-medium text-gray-900 ">{{ __('frontend.full_name') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 " 
                                placeholder="{{ __('frontend.enter_full_name') }}" required />
                            @error('name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 ">{{ __('frontend.email') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 "
                                placeholder="{{ __('frontend.enter_email') }}" required />
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="mobile"
                                class="block mb-2 text-sm font-medium text-gray-900 ">{{ __('frontend.phone') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="tel" id="mobile" name="mobile" value="{{ old('mobile') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 "
                                placeholder="{{ __('frontend.enter_phone_number') }}" required />
                            @error('mobile')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label for="subject"
                                class="block mb-2 text-sm font-medium text-gray-900 ">{{ __('frontend.subject') }}<span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 "
                                placeholder="{{ __('frontend.enter') }}" required />
                            <small id="subjectCount" class="float-right">0/255</small>
                            @error('subject')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="message"
                                class="block mb-2 text-sm font-medium text-gray-900 ">{{ __('frontend.message') }}<span
                                    class="text-red-500">*</span></label>
                            <textarea id="message" name="message" rows="6"
                                class="block p-3.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 "
                                placeholder="{{ __('frontend.message') }}">{{ old('message') }}</textarea>
                            <small id="messageCount" class="float-right">0/2000</small>
                            @error('message')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit"
                            class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md px-8 py-4 text-center transition-colors duration-200">{{ __('frontend.submit') }}</button>
                    </form>
                </div>

                <div class="space-y-8 mb-8 xl:mb-0">
                    <div class="border rounded-lg p-6 border-gray-300">
                        <h3 class="mb-2 text-[30px] text-[#034833] font-cinzel">
                            {{ $pageData?->getTranslation('subtitle', $lang) }}</h3>
                        <p class="text-gray-600">{!! $pageData?->getTranslation('description', $lang) !!}</p>
                        <ul class="mt-4 space-y-2 text-gray-700">
                            <li><strong>{{ __('frontend.email') }}:</strong> info@justyta.com</li>
                            <li><strong>{{ __('frontend.address') }}:</strong> Sharjah, UAE</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>




    </section>
@endsection

@section('script')
    <script>
        const subjectInput = document.querySelector('[name="subject"]');
        const maxSubjectLength = 255;

        subjectInput.addEventListener('input', function () {
            if (this.value.length > maxSubjectLength) {
                this.value = this.value.slice(0, maxSubjectLength);
            }

            document.getElementById('subjectCount').innerText =
                this.value.length + '/' + maxSubjectLength;
        });

        const messageInput = document.querySelector('[name="message"]');
        const maxMessageLength = 2000;

        messageInput.addEventListener('input', function () {
            if (this.value.length > maxMessageLength) {
                this.value = this.value.slice(0, maxMessageLength);
            }

            document.getElementById('messageCount').innerText =
                this.value.length + '/' + maxMessageLength;
        });

        const nameInput = document.querySelector('[name="name"]');
        const maxNameLength = 150;

        nameInput.addEventListener('input', function () {
            if (this.value.length > maxNameLength) {
                this.value = this.value.slice(0, maxNameLength);
            }
        });

        const emailInput = document.querySelector('[name="email"]');
        const maxEmailLength = 100;

        emailInput.addEventListener('input', function () {
            if (this.value.length > maxEmailLength) {
                this.value = this.value.slice(0, maxEmailLength);
            }
        });

        const mobileInput = document.querySelector('[name="mobile"]');
        const maxMobileLength = 20;

        mobileInput.addEventListener('input', function () {
            if (this.value.length > maxMobileLength) {
                this.value = this.value.slice(0, maxMobileLength);
            }
        });
          
    </script>
@endsection