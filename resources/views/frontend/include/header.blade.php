    <header class="container mx-auto xl:mt-5 px-5 sticky xl:static top-0 bg-white xl:bg-transparent py-3 xl:py-0 z-50">
        <div class="flex items-start justify-between md:justify-start">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" class="hidden xl:block" alt="Logo">
            </a>
            <nav class="flex items-center justify-between px-0 xl:px-6 py-0 xl:py-4">

                <button id="btnMenuOpen" class="xl:hidden py-0 xl:py-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none"
                        stroke="black" stroke-width="2">
                        <path d="M4 8h24M4 16h24M4 24h24"></path>
                    </svg>
                </button>

                <div id="mobileNavPanel"
                    class="fixed xl:static top-0 left-0 h-full xl:h-auto w-64 xl:w-auto
           bg-white xl:bg-transparent shadow-xl xl:shadow-none
           transform -translate-x-full xl:translate-x-0
           transition-transform duration-300 p-10 xl:p-0 z-[9999]">

                    <button id="btnMenuClose"
                        class="xl:hidden absolute top-4 right-4 text-2xl bg-white shadow p-2 rounded-full">
                        ✕
                    </button>

                    <ul id="navMenuList" class="flex flex-col xl:flex-row gap-8 font-cinzel font-bold text-[16px]">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('assets/images/logo.png') }}" class="block xl:hidden" width="80" alt="Logo">
                        </a>

                        <li><a href="{{ route('home') }}" class="text-[#07683B]">{{ __('frontend.home') }}</a></li>
                        <li><a href="{{ route('aboutus') }}" class="text-[#07683B]">{{ __('frontend.about_us') }}</a></li>
                        <li><a href="{{ route('services') }}" class="text-[#07683B]">{{ __('frontend.services') }}</a></li>
                        <li><a href="{{ route('news') }}" class="text-[#07683B]">{{ __('frontend.news') }}</a></li>
                        <li><a href="{{ route('contactus') }}" class="text-[#07683B]">{{ __('frontend.contact') }}</a></li>
                    </ul>
                </div>
            </nav>

            <div class="flex md:flex items-center gap-4 ms-auto ">

                @guest('frontend')
                    <a href="{{ route('frontend.login') }}" class="bg-[#04502E] block text-[8px] xl:text-[16px] text-white px-4 xl:px-8 py-2 rounded-full w-auto ">
                        {{ __('frontend.sign_in') }}

                    </a>

                    <div x-data="{ open: false }" class="relative inline-block">
                        <button @click="open = !open"
                            class="flex items-center gap-2 text-[#07683B] border text-[8px] xl:text-[16px] !border-[#07683B] px-4 xl:px-6 py-1 xl:py-2 rounded-full w-auto">

                            {{ __('frontend.sign_up') }}

                            <svg :class="open ? 'rotate-180' : 'rotate-0'"
                                class="w-4 h-4 transition-transform duration-200"
                                fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 bg-white border rounded-lg shadow-lg w-44 z-50 py-1">

                            <a href="{{ route('frontend.register') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('frontend.user_signup') }}
                            </a>

                            <a href="{{ route('law-firm.register') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ __('frontend.lawfirm_signup') }}
                            </a>

                        </div>
                    </div>
                @endguest

                @auth('frontend')
                    @php
                        $user = Auth::guard('frontend')->user();
                   
                        switch ($user->user_type) {
                            case 'lawyer':
                                $myaccountRoute = route('lawyer.dashboard');
                                break;
                            case 'vendor':
                                $myaccountRoute = route('vendor.dashboard');
                                break;
                            case 'translator':
                                $myaccountRoute = route('translator.dashboard');
                                break;
                            default:
                                $myaccountRoute = route('user.dashboard');
                                break;
                        }
                    @endphp     
                    <a href="{{ $myaccountRoute }}" class="bg-[#04502E] block text-[8px] xl:text-[16px] text-white px-4 xl:px-8 py-2 rounded-full w-auto">
                        {{ __('frontend.my_account') }}
                    </a>

                    <a href="{{ route('frontend.logout') }}" class="gap-2 text-[#07683B] border text-[8px] xl:text-[16px] !border-[#07683B] px-4 xl:px-6 py-1 xl:py-2 rounded-full w-auto">
                        {{ __('frontend.sign_out') }}

                    </a>
                @endauth



                


                <div class="relative">
                    {{-- <button type="button"
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
                    </button> --}}
                    <div class="relative inline-block text-left">
                        <button id="langDropdownBtn" data-dropdown-toggle="langDropdown"
                            class="uppercase inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md">
                            {{ app()->getLocale() }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="langDropdown" class="hidden z-10 mt-2 w-15 divide-y bg-white divide-gray-100 rounded-lg shadow">
                            <ul class="py-2 text-sm text-gray-700" aria-labelledby="langDropdownBtn">
                                <li><a href="{{ route('lang.switch', 'en') }}" onclick="localStorage.setItem('lang', 'en')" class="block px-4 py-2 hover:bg-gray-100">EN</a></li>
                                <li><a href="{{ route('lang.switch', 'ar') }}" onclick="localStorage.setItem('lang', 'ar')" class="block px-4 py-2 hover:bg-gray-100">AR</a></li>
                                <li><a href="{{ route('lang.switch', 'fr') }}" onclick="localStorage.setItem('lang', 'fr')" class="block px-4 py-2 hover:bg-gray-100">FR</a></li>
                                <li><a href="{{ route('lang.switch', 'fa') }}" onclick="localStorage.setItem('lang', 'fa')" class="block px-4 py-2 hover:bg-gray-100">FA</a></li>
                                <li><a href="{{ route('lang.switch', 'ru') }}" onclick="localStorage.setItem('lang', 'ru')" class="block px-4 py-2 hover:bg-gray-100">RU</a></li>
                                <li><a href="{{ route('lang.switch', 'zh') }}" onclick="localStorage.setItem('lang', 'zh')" class="block px-4 py-2 hover:bg-gray-100">ZH</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </header>