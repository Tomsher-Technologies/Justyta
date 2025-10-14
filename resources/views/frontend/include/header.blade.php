    <header class="container mx-auto mt-5 px-5 xl:px-0">
        <div class="flex items-start justify-between md:justify-start">
            <a href="{{ route('home') }}" >
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" >
            </a>
            <ul class="flex items-center gap-8 font-cinzel font-bold text-[16px] ms-auto mt-3">
                <li>
                    <a href="{{ route('home') }}" class="text-[#07683B]">{{ __('frontend.home') }}</a>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="text-[#07683B]">{{ __('frontend.about_us') }}</a>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="text-[#07683B]">{{ __('frontend.services') }}</a>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="text-[#07683B]">{{ __('frontend.news') }}</a>
                </li>
                <li>
                    <a href="{{ route('home') }}" class="text-[#07683B]">{{ __('frontend.contact') }}</a>
                </li>
            </ul>
            <div class="flex md:flex items-center gap-4 ms-auto">
               
                    <a href="{{ route('frontend.login') }}" class="bg-[#04502E] text-white px-8 py-2 rounded-full w-auto ">
                        {{ __('frontend.sign_in') }}
                    </a>
                    <a href="{{ route('frontend.register') }}" class="text-[#07683B] border !border-[#07683B] px-8 py-2 rounded-full w-auto ">
                        {{ __('frontend.sign_up') }}
                    </a>
               
                <div class="relative">
                    <button type="button"
                        class="relative inline-flex items-center text-sm font-medium text-center text-black rounded-lg w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="33" viewBox="0 0 28 33"
                            fill="none">
                            <path
                                d="M18.6965 25.8946V27.0701C18.6965 28.3171 18.2012 29.513 17.3194 30.3948C16.4376 31.2765 15.2417 31.7719 13.9947 31.7719C12.7477 31.7719 11.5517 31.2765 10.67 30.3948C9.7882 29.513 9.29283 28.3171 9.29283 27.0701V25.8946M26.6074 23.5018C24.7208 21.1927 23.3888 20.0173 23.3888 13.6514C23.3888 7.82183 20.412 5.74493 17.9619 4.73623C17.6364 4.60252 17.3301 4.29543 17.2309 3.96116C16.8011 2.49844 15.5963 1.20984 13.9947 1.20984C12.3931 1.20984 11.1875 2.49918 10.7622 3.96263C10.663 4.30058 10.3566 4.60252 10.0312 4.73623C7.57812 5.7464 4.60419 7.81595 4.60419 13.6514C4.60052 20.0173 3.26857 21.1927 1.38195 23.5018C0.600268 24.4583 1.28498 25.8946 2.65219 25.8946H25.3445C26.7044 25.8946 27.3847 24.4539 26.6074 23.5018Z"
                                stroke="#3B3A3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full -top-2 -end-2 shadow-xl">
                            20
                        </div>
                    </button>
                    <div class="relative inline-block text-left">
                        <button id="langDropdownBtn" data-dropdown-toggle="langDropdown"
                            class="uppercase inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md">
                            {{ app()->getLocale() }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="langDropdown" class="hidden z-10 mt-2 w-28 divide-y divide-gray-100 rounded-lg shadow">
                            <ul class="py-2 text-sm text-gray-700" aria-labelledby="langDropdownBtn">
                                <li><a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 hover:bg-gray-100">EN</a></li>
                                <li><a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 hover:bg-gray-100">AR</a></li>
                                <li><a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 hover:bg-gray-100">FR</a></li>
                                <li><a href="{{ route('lang.switch', 'fa') }}" class="block px-4 py-2 hover:bg-gray-100">FA</a></li>
                                <li><a href="{{ route('lang.switch', 'ru') }}" class="block px-4 py-2 hover:bg-gray-100">RU</a></li>
                                <li><a href="{{ route('lang.switch', 'zh') }}" class="block px-4 py-2 hover:bg-gray-100">ZH</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
          
        </div>
    </header>