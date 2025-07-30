@extends('layouts.web_default', ['title' =>  __('frontend.law_firm_jobs') ])

@section('content')

<div class="bg-white rounded-2xl p-8 pb-12">
    <div class="flex justify-between  items-center mb-2">
        
        <div class="flex">
            <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.service_request_details') }}</h2>
            @php
                $statusClass = [
                    'pending'   => '!bg-[#bdbdbdb5] !text-[#444444] dark:bg-gray-800 dark:text-gray-300',
                    'ongoing'   => '!bg-[#ffdb82] !text-[#000000] dark:bg-yellow-900 dark:text-yellow-300',
                    'completed' => '!bg-[#42e1428c] !text-[#1B5E20] dark:bg-green-900 dark:text-green-300',
                    'rejected'  => '!bg-[#fca6a6a1] !text-[#B71C1C] dark:bg-red-900 dark:text-red-300',
                    
                ];
                $paymentStatus = [
                    'pending'   => '!bg-[#ea1616] !text-[#fff] dark:bg-gray-800 dark:text-gray-300',
                    'success'   => '!bg-[#008000] !text-[#fff] dark:bg-green-900 dark:text-green-300',
                    'failed'    => '!bg-[#ea1616] !text-[#fff] dark:bg-red-900 dark:text-red-300',
                    'partial'   => '!bg-[#ffdb82] !text-[#000000] dark:bg-yellow-900 dark:text-yellow-300',
                ];
            @endphp
           
            @php
                $status = strtolower($dataService['status']);
                $payStatus = strtolower($dataService['payment_status']); 
            @endphp

            <span class="{{ $statusClass[$status] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-5 py-2 rounded-full ml-2">
                {{ ucfirst($status) }}
            </span>
        </div>
        <a href="{{ Session::has('service_last_url') ? Session::get('service_last_url') : route('user.service.history') }}"
                    class="inline-flex items-center px-6 py-3 text-black bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                    {{ __('frontend.go_back') }}
                <svg class="w-4 h-4 ms-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10" aria-hidden="true">
                    <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5H1m0 0l4-4M1 5l4 4" />
                </svg>
            </a>
    </div>

    <hr class="my-4 border-[#DFDFDF]">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">
        <!-- Left Side: Fields -->
        <div class="border-r col-span-3">
            <div class="space-y-6">
                <div class="flex items-center">
                    <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.application_reference_number') }}</p>
                    <p class="basis-3/5 text-gray-800">{{ $dataService['reference_code'] }}</p>
                </div>

                <div class="flex items-center">
                    <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.service') }}</p>
                    <p class="basis-3/5 text-gray-800">{{ $dataService['service_name'] }}</p>
                </div>
                <div class="flex items-center">
                    <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.submitted_date') }}</p>
                    <p class="basis-3/5 text-gray-800">{{ date('d M, Y h:i A', strtotime($dataService['submitted_at'])) }}</p>
                </div>

                @if($dataService['payment_status'] != NULL)
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.amount') }}</p>
                        <p class="basis-3/5 text-gray-800">{{ __('frontend.AED') }} {{ number_format($dataService['amount'], 2) }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.payment_status') }}</p>
                        <p class="basis-3/5 text-gray-800">
                            <span class="{{ $paymentStatus[$payStatus] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-4 py-1 rounded-full ml-2">
                                @if ($payStatus == 'success')
                                    {{ __('frontend.paid') }}
                                @elseif ($payStatus == 'partial')
                                    {{ __('frontend.partial') }}
                                @else
                                    {{ __('frontend.un_paid') }}
                                @endif
                            </span>
                        </p>
                    </div>
                @endif

             

                @foreach($dataService['service_details'] as $key => $value)
                    @if(!is_array($value)) {{-- Only simple fields --}}
                        <div class="flex "> {{--items-center--}}
                            <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.'.$key) }}</p>
                            <p class="basis-3/5 text-gray-800">
                                @if (Str::startsWith($value, '[') && Str::endsWith($value, ']'))
                                    @php
                                        $decodedValue = json_decode($value, true);
                                    @endphp
                                    {{ implode(', ', $decodedValue) }}
                                @else
                                    {{ ucwords($value) }}
                                @endif
                            </p>
                        </div>
                    @endif
                @endforeach


                @if(!empty($dataService['installments']) && count($dataService['installments']) > 0)
                    <div class="flex items-center">
                        <div class=" border-[#DFDFDF] pt-6 mt-6 basis-3/5">
                            <h3 class="text-md font-semibold text-gray-700 mb-4">{{ __('frontend.payment_details') }}</h3>
                            <div class="space-y-3">
                                @foreach($dataService['installments'] as $installment)
                                    @php
                                        $installmentStatusClass = [
                                            'pending'   => '!bg-[#ea1616] !text-white',
                                            'paid'      => '!bg-[#42e1428c] !text-[#1B5E20]',
                                        ];
                                        $iStatus = strtolower($installment['status']);
                                    @endphp
                                    <div class="flex items-center justify-between border-t bg-gray-50 rounded-lg px-4 py-2">
                                        <p class="text-sm text-gray-700 font-medium">
                                            {{ __('frontend.installment') }} {{ $installment['installment_no'] }}: 
                                            <span class="ml-2 text-gray-900 font-semibold">{{ __('frontend.AED') }} {{ number_format($installment['amount'], 2) }}</span>
                                        </p>
                                        <span class=" {{ $installmentStatusClass[$iStatus] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-3 py-1 rounded-full">
                                            {{ ucfirst($iStatus) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>


        
        <!-- Right Side: Files -->
        <div class="col-span-2">
            <h3 class="mb-3 font-medium">{{ __('frontend.uploaded_documents') }}:</h3>
            <div class="space-y-4">
                @foreach($dataService['service_details'] as $key => $files)
                    @if(is_array($files) && !empty($files))
                       
                        <div>
                            <p class="text-gray-600 font-medium mb-2">{{ __('frontend.'.$key) }} :</p>
                            <div class="flex flex-wrap gap-3">
                                @foreach($files as $index => $file)
                                    @php $isImage = Str::endsWith($file, ['.png', '.jpg', '.jpeg', '.webp']); @endphp
                                   
                                    @if($isImage)
                                        <a data-fancybox="gallery" href="{{ $file }}">
                                            <img src="{{ $file }}" class="h-28 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="">
                                        </a>
                                    @else
                                        <a href="{{ $file }}"  data-fancybox="gallery">
                                            <img src="{{ asset('assets/images/file.png') }}" class="h-28 object-cover rounded-lg border border-gray-300 hover:opacity-75" alt="">
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>



@endsection