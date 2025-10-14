@extends('layouts.web_login', ['title' => 'Refund Policy'])

@section('content')    

    <section class="py-[40px] md:py-[0px] px-20 md:px-10 lg:px-20 xl:px-0">
      <div class="container m-auto px-20">
         <!-- Title -->
         <h1 class="mb-5 text-5xl text-[#034833] font-cinzel text-center">Refund Policy</h1>
         <!-- Meta -->
        
         <!-- Blog Content -->
         <article class="prose prose-lg max-w-none">
            
            {{-- <ul class="mb-5 gap-10-px">
               <li><strong>Right to Representation:</strong> Seek legal support to guide you through the process.</li>
               <li><strong>Right to File a Lawsuit:</strong> If mediation fails, take legal action in court.</li>
               <li><strong>Right to a Fair Hearing:</strong> Both sides can present their arguments fairly.</li>
            </ul> --}}

            <div>
                <p class="mb-5">
                Effective Date: 14/10/2025 <br>
                Company: Justyta FZE <br>
                Jurisdiction: United Arab Emirates <br>
                </p>

                <p class="mb-5">
                    1. General Policy
                    All payments made through the Justyta mobile application are final and non-refundable except in specific cases described below. Justyta operates as an electronic platform connecting clients with licensed service providers (lawyers, translators, and other professionals).
                </p>

                <p class="mb-5">

                    2. Eligible Refund Cases
                    Refunds may be approved under the following circumstances:

                    <ul class="mb-5 gap-10-px ml-10" style=" list-style: disc;">
                        <li><strong>Duplicate Payment:</strong> If a client is charged more than once for the same service.</li>
                        <li><strong>Service Unavailability:</strong> If the booked service cannot be delivered due to provider cancellation or technical error.</li>
                        <li><strong>Payment Error:</strong> If a transaction is proven to be processed in error by the payment gateway.</li>
                    </ul>
                </p>

                 <p class="mb-5">
                    3. Non-Refundable Services

                    <ul class="mb-5 gap-10-px ml-10" style=" list-style: disc;">
                        <li>Completed consultations, translations, or government submissions are strictly non-refundable once delivered or initiated.</li>
                        <li>Refunds will not be issued for user error, change of mind, or dissatisfaction after service completion.</li>
                    </ul>                    
                 </p>

                 <p class="mb-5">
                    4. Refund Process
                    To request a refund, the client must submit a written request within 7 calendar days of the transaction through the in-app “Support” section or by email at info@justyta.com.

                    <ul class="mb-5 gap-10-px ml-10" style=" list-style: disc;">
                        <li>Approved refunds will be processed within 10–15 business days back to the original payment method.</li>
                        <li>Refunds are subject to bank or gateway processing timelines and applicable transaction fees.</li>
                    </ul> 
                   
                 </p>

                 <p class="mb-8">
                    5. Contact Information <br>
                    For all refund inquiries:<br><br>
                    Email: <b>info@justyta.com</b><br>
                    Phone: <b>+971566116711</b><br>
                    Address: <b>JUSTYAT FZE - PUBLICITY CITY</b><br>

                 </p>
            </div>
         </article>





      </div>
   </section>

@endsection