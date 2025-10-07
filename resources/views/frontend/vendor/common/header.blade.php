<nav class="grid grid-cols-2 gap-5 grid-cols-[2fr_1fr] items-center justify-between mb-5">
  

    <div class="relative hidden lg:block w-full">
        <div class="absolute inset-y-0 start-0 flex items-center ps-6 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
        </div>
        <input type="text" id="search-navbar"
            class="block p-3 w-full ps-12 text-sm text-gray-900 border border-[#FFE9B1] rounded-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Search services..." autocomplete="off" />

        <!-- Suggestions container -->
        <div id="search-suggestions"
            class="absolute z-10 w-full bg-white border border-gray-200 rounded-b-lg shadow-md max-h-60 overflow-y-auto hidden">
            <!-- Results will be inserted here -->
        </div>
    </div>

    <div class="flex items-center justify-end gap-4">
        <button type="button"
            class="relative inline-flex items-center text-sm font-medium text-center text-black rounded-lg w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="33" viewBox="0 0 28 33" fill="none">
                <path
                    d="M18.6965 25.8946V27.0701C18.6965 28.3171 18.2012 29.513 17.3194 30.3948C16.4376 31.2765 15.2417 31.7719 13.9947 31.7719C12.7477 31.7719 11.5517 31.2765 10.67 30.3948C9.7882 29.513 9.29283 28.3171 9.29283 27.0701V25.8946M26.6074 23.5018C24.7208 21.1927 23.3888 20.0173 23.3888 13.6514C23.3888 7.82183 20.412 5.74493 17.9619 4.73623C17.6364 4.60252 17.3301 4.29543 17.2309 3.96116C16.8011 2.49844 15.5963 1.20984 13.9947 1.20984C12.3931 1.20984 11.1875 2.49918 10.7622 3.96263C10.663 4.30058 10.3566 4.60252 10.0312 4.73623C7.57812 5.7464 4.60419 7.81595 4.60419 13.6514C4.60052 20.0173 3.26857 21.1927 1.38195 23.5018C0.600268 24.4583 1.28498 25.8946 2.65219 25.8946H25.3445C26.7044 25.8946 27.3847 24.4539 26.6074 23.5018Z"
                    stroke="#3B3A3A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
            <div
                class="absolute inline-flex items-center justify-center w-6 h-6 text-xs text-white bg-red-500 border-2 border-[#E6DFCC] rounded-full -top-2 -end-2 shadow-xl">
                {{ getUnreadNotificationCount() }}
            </div>
        </button>
        <button id="dropdownDefaultButton" data-dropdown-toggle="dropdown"
            class="text-gray-700 hover:bg-gray-100 focus:ring-0 focus:outline-none font-medium rounded-lg text-sm px-2 py-1 text-center inline-flex items-center"
            type="button">
            <span class="fi fi-gb text-lg uppercase">{{ app()->getLocale() }}</span>
            <svg class="w-2.5 h-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>
        </button>
        <div id="dropdown" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-32">
            <ul class="py-2 text-sm text-gray-700" aria-labelledby="dropdownDefaultButton">
                <li><a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 hover:bg-gray-100">EN</a></li>
                <li><a href="{{ route('lang.switch', 'ar') }}" class="block px-4 py-2 hover:bg-gray-100">AR</a></li>
                <li><a href="{{ route('lang.switch', 'fr') }}" class="block px-4 py-2 hover:bg-gray-100">FR</a></li>
                <li><a href="{{ route('lang.switch', 'fa') }}" class="block px-4 py-2 hover:bg-gray-100">FA</a></li>
                <li><a href="{{ route('lang.switch', 'ru') }}" class="block px-4 py-2 hover:bg-gray-100">RU</a></li>
                <li><a href="{{ route('lang.switch', 'zh') }}" class="block px-4 py-2 hover:bg-gray-100">ZH</a></li>
            </ul>
        </div>

        <button id="userDropdownButton" data-dropdown-toggle="userDropdown"
            class="flex items-center p-3 px-5 space-x-2 text-white bg-[#04502E] hover:bg-[#023A21] p-1 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21" fill="none">
                <g clip-path="url(#clip0_331_567)">
                    <path
                        d="M3.10857 3.7861C5.09174 1.89737 7.7286 0.857178 10.5332 0.857178C13.338 0.857178 15.9747 1.89737 17.9578 3.7861C19.941 5.67484 21.0332 8.18597 21.0332 10.8572C21.0332 13.5282 19.941 16.0395 17.9578 17.9283C15.9747 19.817 13.338 20.8572 10.5332 20.8572C7.7286 20.8572 5.09174 19.817 3.10857 17.9283C1.1254 16.0395 0.0332031 13.5282 0.0332031 10.8572C0.0332031 8.18597 1.1254 5.67484 3.10857 3.7861ZM15.7693 18.1379C15.3301 15.7298 13.139 13.9535 10.5332 13.9535C7.92727 13.9535 5.7363 15.7298 5.29714 18.1379C6.78796 19.1134 8.59169 19.6853 10.5332 19.6853C12.4747 19.6853 14.2784 19.1134 15.7693 18.1379ZM13.8718 9.60199C13.8718 7.8486 12.3741 6.42236 10.5332 6.42236C8.69231 6.42236 7.1946 7.84875 7.1946 9.60199C7.1946 11.3552 8.69231 12.7816 10.5332 12.7816C12.3741 12.7816 13.8718 11.3552 13.8718 9.60199ZM4.23138 17.3251C4.56255 16.204 5.22024 15.1869 6.14213 14.3923C6.7077 13.9048 7.35145 13.5201 8.04359 13.249C6.79261 12.4719 5.96397 11.1274 5.96397 9.60199C5.96397 7.20255 8.01379 5.25049 10.5332 5.25049C13.0526 5.25049 15.1023 7.20255 15.1023 9.60199C15.1023 11.1274 14.2736 12.4719 13.0228 13.249C13.7148 13.5201 14.3587 13.9047 14.9243 14.3922C15.846 15.1867 16.5039 16.2039 16.835 17.3249C18.6591 15.7116 19.8027 13.4094 19.8027 10.8572C19.8027 5.98932 15.6445 2.02905 10.5332 2.02905C5.42195 2.02905 1.26367 5.98932 1.26367 10.8572C1.26367 13.4095 2.4073 15.7118 4.23138 17.3251Z"
                        fill="white" />
                </g>
                <defs>
                    <clipPath id="clip0_331_567">
                        <rect width="21" height="20" fill="white"
                            transform="matrix(-1 0 0 1 21.0332 0.857178)" />
                    </clipPath>
                </defs>
            </svg>
            <span class="hidden sm:block">{{ Auth::guard('frontend')->user()->name ?? NULL }}</span>
            <svg class="w-2.5 h-2.5 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 10 6">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m1 1 4 4 4-4" />
            </svg>
        </button>
        <div id="userDropdown"
            class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow p-6 w-64 !translate-x-[238px] !translate-y-[50px]">
            <a href="{{ route('user.my-account') }}" class="flex items-center justify-between w-full border-b">
                <div class="pb-4">
                    <h3 class="text-[#353434] text-lg mb-0 leading-none">
                        {{ Auth::guard('frontend')->user()->name ?? NULL }}
                    </h3>
                    <small class="text-[#353434] text-xs">{{ Auth::guard('frontend')->user()->email ?? NULL }}</small>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="20" viewBox="0 0 19 20"
                    fill="none">
                    <mask id="mask0_315_5973" style="mask-type: alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                        width="19" height="20">
                        <rect x="19" y="19.6777" width="19" height="19" transform="rotate(180 19 19.6777)"
                            fill="#D9D9D9" />
                    </mask>
                    <g mask="url(#mask0_315_5973)">
                        <path
                            d="M6.33333 2.26139L14.25 10.1781L6.33333 18.0947L4.92813 16.6895L11.4396 10.1781L4.92812 3.6666L6.33333 2.26139Z"
                            fill="#717171" />
                    </g>
                </svg>
            </a>
            <ul class="py-2 text-sm text-gray-700 mb-0" aria-labelledby="userDropdownButton">
                <li class="border-b">
                    <a href="#" class="flex items-center gap-2 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="23" viewBox="0 0 28 30"
                            fill="none">
                            <path
                                d="M4.14415 14.5585C4.04215 16.4956 4.15952 18.5576 2.41716 19.855C2.01658 20.1527 1.69152 20.5392 1.46787 20.9837C1.24422 21.4281 1.12815 21.9183 1.12891 22.4152C1.12891 23.7987 2.22155 24.9781 3.64394 24.9781H23.7642C25.1866 24.9781 26.2792 23.7987 26.2792 22.4152C26.2792 21.4078 25.8014 20.4587 24.991 19.855C23.2486 18.5576 23.366 16.4956 23.264 14.5585C23.1382 12.1247 22.0762 9.83187 20.2971 8.1534C18.5181 6.47493 16.1579 5.53906 13.7041 5.53906C11.2502 5.53906 8.89004 6.47493 7.11099 8.1534C5.33195 9.83187 4.26989 12.1247 4.14415 14.5585Z"
                                stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                            <path
                                d="M17.8952 24.9782C17.8952 26.0822 17.4535 27.1411 16.6674 27.9218C15.8813 28.7024 14.8151 29.141 13.7034 29.141C12.5917 29.141 11.5255 28.7024 10.7394 27.9218C9.95334 27.1411 9.51172 26.0822 9.51172 24.9782M11.6076 2.94974C11.6076 4.09869 12.5465 5.55153 13.7034 5.55153C14.8603 5.55153 15.7993 4.09869 15.7993 2.94974C15.7993 1.80079 14.8603 1.38867 13.7034 1.38867C12.5465 1.38867 11.6076 1.80079 11.6076 2.94974Z"
                                stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>

                        <span>{{ __('frontend.notifications') }}</span>
                    </a>
                </li>
                
               
                <li class="mt-2">
                    <a href="{{ route('frontend.logout') }}" class="flex items-center gap-2 py-2">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21"
                            fill="none">
                            <path
                                d="M4.84918 17.5659C4.45845 17.5659 4.13247 17.4352 3.87123 17.174C3.60998 16.9128 3.47908 16.5865 3.47852 16.1952V5.36554C3.47852 4.97481 3.60942 4.64882 3.87123 4.38758C4.13303 4.12634 4.45902 3.99544 4.84918 3.99487H10.2801V4.84306H4.84918C4.71856 4.84306 4.59869 4.89734 4.48955 5.00591C4.38042 5.11448 4.32614 5.23436 4.3267 5.36554V16.196C4.3267 16.3261 4.38099 16.4457 4.48955 16.5548C4.59812 16.6639 4.71772 16.7182 4.84834 16.7177H10.2801V17.5659H4.84918ZM14.0486 13.7821L13.4532 13.1714L15.4201 11.2045H7.8823V10.3563H15.4201L13.4523 8.38848L14.0478 7.77948L17.0495 10.7804L14.0486 13.7821Z"
                                fill="black" />
                        </svg>
                        <span>{{ __('frontend.sign_out') }}</span>

                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
