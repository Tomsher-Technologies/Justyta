@extends('layouts.web_vendor_default', ['title' => __('frontend.lawyers')])

@section('content')
    <div class="bg-white rounded-2xl  p-8 pb-12">
            <div class="flex justify-between items-center mb-8 border-b pb-5">
               <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.lawyers') }}</h2>
               <a href="{{ route('vendor.create.lawyers') }}" class="text-white bg-[#07683B] rounded-full py-2.5 px-6">
                  {{ __('frontend.create_lawyer') }}
               </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4 mb-8">
               <div class="relative col-span-8">
                  <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                     <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                           d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                     </svg>
                  </div>
                  <input type="text" id="simple-search"
                     class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3.5"
                     placeholder="Search here" required />
               </div>
               <div class="col-span-2">
                  <form class="mx-auto">
                     <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">Spatialities</label>
                     <select id="countries"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option selected>Select by spatialities</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="FR">France</option>
                        <option value="DE">Germany</option>
                     </select>
                  </form>
               </div>
               <div class="col-span-2">
                  <form class="mx-auto">
                     <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                     <select id="countries"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option selected>All</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="FR">France</option>
                        <option value="DE">Germany</option>
                     </select>
                  </form>
               </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
               <div class="bg-white rounded-lg border border-[#DDD3B9] p-6">
                  <div class="flex items-middle gap-6 w-full">
                     <img class="h-[130px] rounded-full object-cover"
                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png"
                        alt="Jackson Carter Avatar">
                     <div>
                        <div class="border-b pb-4 mb-4">
                           <h3 class="text-lg font-semibold text-gray-900">Jackson Carter</h3>
                           <p class="text-sm text-gray-500">JC-20250523</p>
                        </div>
                        <div class="text-sm text-gray-700">
                           <p>Last login : <span class="font-medium">2025-05-22 14:30:00</span></p>
                           <p>No. of Consultation : <span class="font-medium">20</span></p>
                        </div>
                     </div>
                  </div>
               </div>
              
               
               <div class="bg-gray-100 opacity-50 rounded-lg border border-[#DDD3B9] p-6" disabled>
                  <div class="flex items-middle gap-6 w-full">
                     <img class="h-[130px] rounded-full object-cover"
                        src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/avatars/jese-leos.png"
                        alt="Jackson Carter Avatar">
                     <div>
                        <div class="border-b pb-4 mb-4">
                           <h3 class="text-lg font-semibold text-gray-900">Jackson Carter</h3>
                           <p class="text-sm text-gray-500">JC-20250523</p>
                        </div>
                        <div class="text-sm text-gray-700">
                           <p>Last login : <span class="font-medium">2025-05-22 14:30:00</span></p>
                           <p>No. of Consultation : <span class="font-medium">20</span></p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
@endsection

