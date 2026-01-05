 <footer class="bg-[#4D1717] px-4 xl:px-[100px] py-[40px] xl:py-[80px] text-white" id="footer">
     <div class="grid grid-cols-2 xl:grid-cols-4 gap-y-10 xl:gap-y-[unset]">
         <div class="col-span-2 xl:col-span-1">
             @php
                $lang = app()->getLocale() ??   'en';
                $settings = \App\Models\WebsiteSetting::pluck('value', 'key')->toArray();
             @endphp
             <h3 class="font-cinzel font-bold text-[20px] mb-5">{{ setting('block_heading_1', $lang, $settings) }}</h3>
             <p class="mb-5">{{ setting('shop_description', $lang, $settings) }}</p>
             <p>{{ setting('footer_copyright', $lang, $settings) }}
                 <br />Designed by <a href="https://www.tomsher.com/"
                     target="_blank">Tomsher</a></p>
         </div>
         <div>
             <h3 class="font-cinzel font-bold text-[20px] mb-5">{{ setting('block_heading_2', $lang, $settings) }}</h3>
             <ul class="flex flex-col items-start gap-2 xl:gap-4">
                 <li>
                     <a href="{{ route('home') }}">{{ __('frontend.home') }}</a>
                 </li>
                 <li>
                     <a href="{{ route('aboutus') }}">{{ __('frontend.about_us') }}</a>
                 </li>
                 <li>
                     <a href="{{ route('services') }}">{{ __('frontend.services') }}</a>
                 </li>
                 <li>
                     <a href="{{ route('contactus') }}">{{ __('frontend.contact_us') }}</a>
                 </li>
             </ul>
         </div>
         <div>
             <h3 class="font-cinzel font-bold text-[20px] mb-5">{{ setting('block_heading_3', $lang, $settings) }}</h3>
             <ul class="flex flex-col items-start gap-2 xl:gap-4">

                 <li>
                     <a href="{{ route('terms-conditions') }}">{{ __('frontend.terms') }}</a>
                 </li>
                 <li>
                     <a href="{{ route('privacy-policy') }}">{{ __('frontend.privacy_policy') }}</a>
                 </li>
                 <li>
                     <a href="{{ route('refund-policy') }}">{{ __('frontend.refund_policy') }}</a>
                 </li>

             </ul>
         </div>
         <div class="col-span-2 xl:col-span-1">
             <h3 class="font-cinzel font-bold text-[20px] mb-5">{{ setting('block_heading_4', $lang, $settings) }}</h3>
             <ul class="flex flex-col items-start gap-2 xl:gap-4">
                 <li>
                     <a href="mailto:{{ $settings['email'] }}">{{ $settings['email'] }}</a>
                 </li>
                 <li> {!! nl2br(setting('address', $lang, $settings)) !!}</li>
             </ul>

             <ul class="flex items-center gap-4 mt-6">
                 @php
                     $footerLinks = \App\Models\WebsiteSetting::where('key', 'footer_links')->value('value');
                     $footerLinks = $footerLinks ? json_decode($footerLinks, true) : [];
                 @endphp

                 @if (!empty($footerLinks))
                     @foreach ($footerLinks as $link)
                         @if (!empty($link['icon']) && !empty($link['url']))
                             <li>
                                 <a href="{{ $link['url'] }}" target="_blank"
                                     class="bg-white block h-8 w-8 xl:h-12 xl:w-12 p-1.5 xl:p-3 rounded-full social-icon-svg"
                                     title="{{ $link['title'] ?? '' }}">
                                     {!! $link['icon'] !!}
                                 </a>
                             </li>
                         @endif
                     @endforeach
                 @endif
             </ul>
         </div>
     </div>
 </footer>
