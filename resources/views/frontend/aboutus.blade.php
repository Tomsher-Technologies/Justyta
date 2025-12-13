
@extends('layouts.web_login', ['title' => 'About Us'])

@section('content') 



        <section class="py-[30px] md:py-[80px] px-5 md:px-5 lg:px-5 xl:px-0">
         <div class="container m-auto">
                 <div class="flex justify-between flex-col xl:flex-row gap-[50px]">
            <img src="images/about-Image.png" alt="">
            <div>
               <h4 class="text-[24px] font-medium text-[#07683B]">About Us</h4>
               <h3 class="text-[26px] leading-[35px] md:text-[40px] font-cinzel font-bold md:leading-[55px] mb-5 text-[#034833]">A New Standard for Legal Access in the UAE </h3>
               <h4 class="text-[30px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">Who We Are </h4>
               <p class="text-[16px] font-medium">We are a UAE-based legal-tech company committed to transforming how individuals and businesses interact with the legal system. Our platform works in partnership with licensed certified legal service providers, certified translators, and industry experts to ensure accuracy, compliance, and trust at every step.
<br/><br/>
From our app to our web platform, every feature is engineered to match the UAE’s digital transformation vision, while supporting both clients and legal professionals with a smoother, smarter experience.
</p><br/>
<h3 class="text-[20px] font-medium text-[#07683B]">Justyta was built with one mission:</h3>
<p class="text-[16px] font-medium">to make legal services faster, clearer, and accessible to everyone, anytime, anywhere with an affordable price.<br/><br/>
In a world where time is valuable and legal procedures are often slow and complicated, Justyta reimagines the entire journey. We combine modern technology, verified legal professionals, automated processes, and user-first design to offer a digital platform that simplifies everything from court submissions to legal consultations.<br/><br/>
We believe justice shouldn’t be delayed by paperwork, crowded offices, or limited working hours.<br/><br/>
Justyta removes the obstacles, leaving only clarity, speed, affordability.</p>

               <br>
               <h4 class="text-[30px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">What We Do </h4>
               <p class="text-[16px] font-medium">Justyta provides a full digital ecosystem of essential legal services:
</p>
               <!--<p class="text-[16px] font-medium">At Lawpoint, we are committed to upholding justice and protecting your rights. With a strong foundation in legal research, advocacy, and client-focused solutions, we provide expert legal services tailored to your needs. </p>-->
               <ul class="mt-6 grid grid-cols-2 gap-3 mb-8">
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Law Firm Services</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Court & Public Prosecution Request Submissions</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Legal Consultations</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Legal Translation</span>
                  </li>
                   <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Training opportunities & Job Listings for Professional Development</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Annual Retainership Agreements for Businesses</span>
                  </li>
               </ul>
               <!--<button class="flex items-center justify-between px-6 pe-4 py-3 bg-[#07683B] text-white rounded-full shadow-lg hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-opacity-50">-->
               <!--   <span class="text-lg font-medium mr-4">Explore Service</span>-->
               <!--   <div class="flex items-center justify-center w-10 h-10 bg-white text-green-700 rounded-full">-->
               <!--      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">-->
               <!--         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>-->
               <!--      </svg>-->
               <!--   </div>-->
               <!--</button>-->
               
               <h4 class="text-[30px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">Our Vision </h4>
               <p class="text-[16px] font-medium">To become the UAE’s leading digital legal hub trusted by clients, preferred by law firms, and recognized for setting a new global benchmark in legal accessibility.
</p><br/>
<h4 class="text-[30px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">Why We Exist</h4>
               <p class="text-[16px] font-medium">Legal services should empower—not overwhelm.
By placing advanced technology behind every step of the legal workflow, Justyta provides:

</p>

 <ul class="mt-6 grid grid-cols-2 gap-3 mb-8">
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Speed without compromise</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Accuracy without delay</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>Transparency without complexity</span>
                  </li>
                  <li class="flex items-center gap-3">
                     <svg class="w-6 h-6 text-[#B9A572]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm13.707-1.293a1 1 0 0 0-1.414-1.414L11 12.586l-1.793-1.793a1 1 0 0 0-1.414 1.414l2.5 2.5a1 1 0 0 0 1.414 0l4-4Z" clip-rule="evenodd"/>
                     </svg>
                     <span>24/7 access without limitations</span>
                  </li>
               </ul>
               
               
               <!--<h4 class="text-[30px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">Why We Exist</h4>-->
               <p class="text-[16px] font-medium">Our purpose is simple:
Bring justice closer to everyone, and give legal professionals a platform that amplifies their reach and impact.

</p><br/>
<p class="text-[18px] font-medium">With Justyta …. Justice Simplified</p>

            </div>
         </div>
         </div>


    
      </section>
      <!--  <section class="bg-[#fff] !pb-0 relative ">-->
      <!--   <div class="container m-auto ">-->
      <!--      <div class="bg-[#FFF9F4] p-0 xl:p-12 md:p-12 h-full px-5 md:px-5 lg:px-5">-->
      <!--      <div class="w-[100%] xl:w-[60%]">-->
      <!--         <h4 class="text-[24px] font-medium text-[#07683B]">Why Choose Us</h4>-->
      <!--         <h3 class="text-[40px] font-cinzel font-bold leading-[55px] mb-5 text-[#034833]">Justice served with <br> Lawpoint — your rights, </h3>-->
      <!--         <p class="text-[16px] font-medium">Working at a law firm helps you develop essential skills, such as legal research, critical thinking, and effective communication.</p>-->
      <!--         <br>-->
      <!--         <p class="text-[16px] font-medium">At Lawpoint, we are committed to upholding justice and protecting your rights. With a strong foundation in legal research, advocacy, and client-focused solutions, we provide expert legal services tailored to your needs. </p>-->
      <!--         <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-8 w-full md:w-max">-->
      <!--            <div class="border-b !border-[#C9C5C1] flex items-center gap-5 pb-8">-->
      <!--               <h3 class="font-cinzel text-[54px] font-bold text-[#B9A572]">100%</h3>-->
      <!--               <h5 class="text-[#000] text-[18px] font-medium">Customer <br> Satisfaction</h5>-->
      <!--            </div>-->
      <!--            <div class="border-b !border-[#C9C5C1] flex items-center gap-5 pb-8">-->
      <!--               <h3 class="font-cinzel text-[54px] font-bold text-[#B9A572]">100%</h3>-->
      <!--               <h5 class="text-[#000] text-[18px] font-medium">Customer <br> Satisfaction</h5>-->
      <!--            </div>-->
      <!--            <div class="border-b !border-[#C9C5C1] flex items-center gap-5 pb-8">-->
      <!--               <h3 class="font-cinzel text-[54px] font-bold text-[#B9A572]">100%</h3>-->
      <!--               <h5 class="text-[#000] text-[18px] font-medium">Customer <br> Satisfaction</h5>-->
      <!--            </div>-->
      <!--            <div class="border-b !border-[#C9C5C1] flex items-center gap-5 pb-8">-->
      <!--               <h3 class="font-cinzel text-[54px] font-bold text-[#B9A572]">100%</h3>-->
      <!--               <h5 class="text-[#000] text-[18px] font-medium">Customer <br> Satisfaction</h5>-->
      <!--            </div>-->
      <!--         </div>-->
      <!--      </div>-->
      <!--   </div>-->
      <!--   <img src="images/law-img.png" class="static xl:absolute end-0 bottom-0  h-auto" alt="">-->
      <!--   </div>-->



      <!--</section>-->

      <!--<section class="bg-[#FFF] py-[40px] md:py-[80px] px-5 md:px-5 lg:px-5 xl:px-0">-->

      <!--   <div class="container m-auto">-->
      <!--              <div class="grid grid-cols-1 xl:grid-cols-2 items-center">-->
      <!--      <div>-->
      <!--         <h3 class="text-[40px] font-cinzel font-bold leading-[55px] mb-5">Justice served with <br> Lawpoint — your rights, </h3>-->
      <!--         <p class="text-[16px] font-medium">Working at a law firm helps you develop essential skills, such as legal research, critical thinking, and effective communication.</p>-->
      <!--         <div class="grid grid-cols-2 gap-3 w-max mt-6">-->
      <!--            <img src="images/play-store.svg" alt="">-->
      <!--            <img src="images/app-store.svg" alt="">-->
      <!--         </div>-->
      <!--      </div>-->
      <!--      <img src="images/app-mockup.png" alt="">-->
      <!--   </div>-->
      <!--   </div>-->
 
      <!--</section>-->


@endsection
