 <footer class="bg-[#4D1717] px-4 xl:px-[100px] py-[40px] xl:py-[80px] text-white" id="footer">
     <div class="grid grid-cols-2 xl:grid-cols-4 gap-y-10 xl:gap-y-[unset]">
         <div class="col-span-2 xl:col-span-1">
             @php
             $aboutDescription = \App\Models\WebsiteSetting::where('key', 'shop_description')->value('value');
             @endphp
             <h3 class="font-cinzel font-bold text-[20px] mb-5">About Us</h3>
             <p class="mb-5">{{ $aboutDescription }}</p>
             <p>Justyta© 2025 All rights reserved. <br />Designed by <a href="https://www.tomsher.com/" target="_blank">Tomsher</a></p>
         </div>
         <div>
             <h3 class="font-cinzel font-bold text-[20px] mb-5">Navigation</h3>
             <ul class="flex flex-col items-start gap-2 xl:gap-4">
                 <li>
                     <a href="{{ route('home') }}">Home</a>
                 </li>
                 <li>
                     <a href="{{ route('aboutus') }}">About Us</a>
                 </li>
                 <li>
                     <a href="{{ route('services') }}">Services</a>
                 </li>
                 <li>
                     <a href="{{ route('contactus') }}">Contact Us</a>
                 </li>
             </ul>
         </div>
         <div>
             <h3 class="font-cinzel font-bold text-[20px] mb-5">Quick Links</h3>
             <ul class="flex flex-col items-start gap-2 xl:gap-4">

                 <li>
                     <a href="{{ route('terms-conditions') }}">Terms and Conditions</a>
                 </li>
                 <li>
                     <a href="{{ route('privacy-policy') }}">Privacy Policy</a>
                 </li>
                 <li>
                     <a href="{{ route('refund-policy') }}">Refund Policy</a>
                 </li>

             </ul>
         </div>
         <div class="col-span-2 xl:col-span-1">
             <h3 class="font-cinzel font-bold text-[20px] mb-5">Contact Us</h3>
             <ul class="flex flex-col items-start gap-2 xl:gap-4">
                 <li>
                     <a href="#">info@justyta.com</a>
                 </li>
                 <li>
                     <a href="#">Sharjah, United Arab Emirates</a>
             </ul>

             <ul class="flex items-center gap-4 mt-6">
                 @php
                 $footerLinks = \App\Models\WebsiteSetting::where('key', 'footer_links')->value('value');
                 $footerLinks = $footerLinks ? json_decode($footerLinks, true) : [];
                 @endphp

                 @if(!empty($footerLinks))
                 @foreach($footerLinks as $link)
                 @if(!empty($link['icon']) && !empty($link['url']))
                 <li>
                     <a href="{{ $link['url'] }}" target="_blank" class="bg-white block h-8 w-8 xl:h-12 xl:w-12 p-1.5 xl:p-3 rounded-full social-icon-svg" title="{{ $link['title'] ?? '' }}">
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