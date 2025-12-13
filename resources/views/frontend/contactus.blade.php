@extends('layouts.web_login', ['title' => 'Contact Us'])

@section('content') 

      <section class="py-[40px] my-14">

         <div class="container m-auto px-5">
                     <h4 class="mb-3 text-[30px] text-[#034833] font-cinzel">Get in Touch</h4>
       <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <div>

            <form>
                <div class="mb-5">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">Email<span class="text-red-500">*</span></label>
                    <input type="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 " placeholder="Enter email" required />
                </div>

                <div class="mb-5">
                    <label for="mobile" class="block mb-2 text-sm font-medium text-gray-900 ">Mobile<span class="text-red-500">*</span></label>
                    <input type="tel" id="mobile" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 " placeholder="Enter mobile" required />
                </div>

                <div class="mb-5">
                    <label for="subject" class="block mb-2 text-sm font-medium text-gray-900 ">Subject<span class="text-red-500">*</span></label>
                    <input type="text" id="subject" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-4 " placeholder="Enter mobile" required />
                </div>

                <div class="mb-6">
                    <label for="message" class="block mb-2 text-sm font-medium text-gray-900 ">Message<span class="text-red-500">*</span></label>
                    <textarea id="message" rows="6" class="block p-3.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 " placeholder="Type here..."></textarea>

                </div>
<button type="button" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md px-8 py-4 text-center transition-colors duration-200">Sumbit</button>
            </form>
        </div>

         <div class="space-y-8 mb-8 xl:mb-0">
      <div class="border rounded-lg p-6 border-gray-300">
        <h3 class="mb-2 text-[30px] text-[#034833] font-cinzel">Contact Info</h3>
        <p class="text-gray-600">You can reach us anytime via the following ways:</p>
        <ul class="mt-4 space-y-2 text-gray-700">
          <li><strong>Email:</strong> info@justyta.com</li>
          <!--<li><strong>Phone:</strong> +1 123 456 7890</li>-->
          <li><strong>Address:</strong> Sharjah, UAE</li>
        </ul>
      </div>
      <!--<div>-->
      <!--  <div class="w-full h-80  rounded-md overflow-hidden">-->
      <!--      <img src="images/contact-img.png" alt="Contact Us Image" class="rounded-lg shadow-md w-full h-auto object-cover max-h-[450px]">-->
      <!--  </div>-->
      <!--</div>-->
    </div>
    </div>
         </div>


@endsection

      </section>
      
      
