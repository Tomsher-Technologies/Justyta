@extends('layouts.web_vendor_default', ['title' => __('frontend.legal_translation_request')])

@section('content')

    <div class="bg-white rounded-2xl p-8 pb-12">
        <div class="flex justify-between  items-center mb-2">

            <div class="flex">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.service_request_details') }}</h2>
                @php
                    $statusClass = [
                        'pending' => '!bg-[#bdbdbdb5] !text-[#444444] dark:bg-gray-800 dark:text-gray-300',
                        'ongoing' => '!bg-[#ffdb82] !text-[#000000] dark:bg-yellow-900 dark:text-yellow-300',
                        'completed' => '!bg-[#42e1428c] !text-[#1B5E20] dark:bg-green-900 dark:text-green-300',
                        'rejected' => '!bg-[#fca6a6a1] !text-[#B71C1C] dark:bg-red-900 dark:text-red-300',
                    ];
                    $paymentStatus = [
                        'pending' => '!bg-[#ea1616] !text-[#fff] dark:bg-gray-800 dark:text-gray-300',
                        'success' => '!bg-[#008000] !text-[#fff] dark:bg-green-900 dark:text-green-300',
                        'failed' => '!bg-[#ea1616] !text-[#fff] dark:bg-red-900 dark:text-red-300',
                        'partial' => '!bg-[#ffdb82] !text-[#000000] dark:bg-yellow-900 dark:text-yellow-300',
                    ];
                @endphp

                @php
                    $status = strtolower($details['status']);
                    $payStatus = strtolower($details['payment_status']);
                @endphp

                <span
                    class="{{ $statusClass[$status] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-5 py-2 rounded-full ml-2">
                    {{ ucfirst($status) }}
                </span>
            </div>
            <a href="{{ Session::has('translation_request_last_url') ? Session::get('translation_request_last_url') : route('vendor.translation-requests') }}"
                class="inline-flex items-center px-4 py-2 text-black bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:ring-green-300 font-medium rounded-full text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                {{ __('frontend.go_back') }}
                <svg class="w-4 h-4 ms-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10"
                    aria-hidden="true">
                    <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5H1m0 0l4-4M1 5l4 4" />
                </svg>
            </a>
        </div>

        @if ($details['status'] === 'rejected' && isset($details['rejection_meta']['rejection_details']))
            @php
                $rejectionDetails = $details['rejection_meta']['rejection_details'];
            @endphp
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">{{ __('frontend.rejection_reason') }}</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>{{ $rejectionDetails['reason'] ?? __('frontend.no_reason_provided') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <hr class="my-4 border-[#DFDFDF]">

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12">
            <!-- Left Side: Fields -->
            <div class="border-r col-span-3">
                <div class="space-y-6">
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.application_reference_number') }}</p>
                        <p class="basis-3/5 text-gray-800">{{ $details['reference_code'] }}</p>
                    </div>

                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.service') }}</p>
                        <p class="basis-3/5 text-gray-800">{{ $details['service_name'] }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.submitted_date') }}</p>
                        <p class="basis-3/5 text-gray-800">
                            {{ date('d M, Y h:i A', strtotime($details['submitted_at'])) }}</p>
                    </div>

                    @if ($details['payment_status'] != null)
                        <div class="flex items-center">
                            <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.amount') }}</p>
                            <p class="basis-3/5 text-gray-800">{{ __('frontend.AED') }}
                                {{ number_format($details['amount'], 2) }}</p>
                        </div>

                        <div class="flex items-center">
                            <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.payment_status') }}</p>
                            <p class="basis-3/5 text-gray-800">
                                <span
                                    class="{{ $paymentStatus[$payStatus] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-4 py-1 rounded-full ml-2">
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



                    @foreach ($details['service_details'] as $key => $value)
                        @if (!is_array($value))
                            {{-- Only simple fields --}}
                            <div class="flex "> {{-- items-center --}}
                                <p class="basis-2/5 text-gray-600 font-medium">{{ __('frontend.' . $key) }}</p>
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


                    @if (!empty($details['installments']) && count($details['installments']) > 0)
                        <div class="flex items-center">
                            <div class=" border-[#DFDFDF] pt-6 mt-6 basis-3/5">
                                <h3 class="text-md font-semibold text-gray-700 mb-4">{{ __('frontend.payment_details') }}
                                </h3>
                                <div class="space-y-3">
                                    @foreach ($details['installments'] as $installment)
                                        @php
                                            $installmentStatusClass = [
                                                'pending' => '!bg-[#ea1616] !text-white',
                                                'paid' => '!bg-[#42e1428c] !text-[#1B5E20]',
                                            ];
                                            $iStatus = strtolower($installment['status']);
                                        @endphp
                                        <div
                                            class="flex items-center justify-between border-t bg-gray-50 rounded-lg px-4 py-2">
                                            <p class="text-sm text-gray-700 font-medium">
                                                {{ __('frontend.installment') }} {{ $installment['installment_no'] }}:
                                                <span class="ml-2 text-gray-900 font-semibold">{{ __('frontend.AED') }}
                                                    {{ number_format($installment['amount'], 2) }}</span>
                                            </p>
                                            <span
                                                class=" {{ $installmentStatusClass[$iStatus] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-3 py-1 rounded-full">
                                                {{ ucfirst($iStatus) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end mt-16">
                        @if ($details['status'] === 'completed')
                            <a href="{{ route('vendor.translation-request.download', ['id' => $details['id']]) }}"
                                class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-10 py-3 transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('frontend.download') }}
                            </a>
                        @elseif ($details['status'] === 'rejected')
                            <div class="w-[500px] text-center mx-auto bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                                <h3 class="text-xl font-semibold text-gray-800 mb-4">
                                    {{ __('frontend.reupload') . ' ' . __('frontend.documents') }}</h3>

                                @if (isset($details['rejection_meta']['rejection_details']))
                                    @php
                                        $rejectionDetails = $details['rejection_meta']['rejection_details'];
                                    @endphp

                                    <form id="reupload-form" enctype="multipart/form-data" class="space-y-4">
                                        @csrf

                                        @if (isset($rejectionDetails['supporting_docs']) && $rejectionDetails['supporting_docs'])
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('frontend.supporting_documents') }} *
                                                </label>
                                                <input type="file" name="supporting_docs"
                                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                                            </div>
                                        @endif

                                        @if (isset($rejectionDetails['supporting_docs_any']) && $rejectionDetails['supporting_docs_any'])
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    {{ __('frontend.supporting_documents_any') }} *
                                                </label>
                                                <input type="file" name="supporting_docs_any"
                                                    class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                                            </div>
                                        @endif

                                        <div class="pt-4">
                                            <button type="button" id="reupload-submit-btn"
                                                class="w-full bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-4 py-2 transition">
                                                {{ __('frontend.reupload') }}
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <p class="text-gray-500">{{ __('frontend.no_specific_requirements') }}</p>
                                @endif
                            </div>

                        @endif
                    </div>
                </div>
            </div>



            <!-- Right Side: Files -->
            <div class="col-span-2">
                <h3 class="mb-3 font-medium">{{ __('frontend.uploaded_documents') }}:</h3>
                <div class="space-y-4">
                    @foreach ($details['service_details'] as $key => $files)
                        @if (is_array($files) && !empty($files))
                            <div>
                                <p class="text-gray-600 font-medium mb-2">{{ __('frontend.' . $key) }} :</p>
                                <div class="flex flex-wrap gap-3">
                                    @foreach ($files as $index => $file)
                                        @php $isImage = Str::endsWith($file, ['.png', '.jpg', '.jpeg', '.webp']); @endphp

                                        @if ($isImage)
                                            <a data-fancybox="gallery" href="{{ $file }}">
                                                <img src="{{ $file }}"
                                                    class="h-28 object-cover rounded-lg border border-gray-300 hover:opacity-75"
                                                    alt="">
                                            </a>
                                        @else
                                            <a href="{{ $file }}" data-fancybox="gallery">
                                                <img src="{{ asset('assets/images/file.png') }}"
                                                    class="h-28 object-cover rounded-lg border border-gray-300 hover:opacity-75"
                                                    alt="">
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="mt-8">
                    @php
                        $timeline = $details['timeline'] ?? [];
                    @endphp

                    <ol class="relative border-l-2 border-[#DFDFDF] ml-3 mt-8">
                        @foreach ($timeline as $step)
                            @php
                                $isCompleted = true;
                                $isRejectedStatus = strtolower($step['label']) == 'rejected' ? true : false;

                                $dotClasses = $isCompleted
                                    ? 'bg-[#EDE5CF] border-[#C7B07A]'
                                    : 'bg-[#DFDFDF] border-[#DFDFDF]';
                                $textClasses = $isCompleted ? 'text-[#B9A572]' : 'text-[#C7C7C7]';
                            @endphp

                            <li class="mb-3 ml-6">
                                <span
                                    class="absolute -left-3 flex items-center justify-center w-4 h-4 rounded-full border-2 {{ $dotClasses }}"></span>
                                <h3 class="font-semibold {{ $textClasses }} text-lg">
                                    {{ $step['label'] }} @if ($isRejectedStatus && isset($step['meta']['rejection_details']['reason']))
                                        - <span
                                            class="text-sm">({{ $step['meta']['rejection_details']['reason'] }})</span>
                                    @endif
                                </h3>
                                @if ($isCompleted && !empty($step['date']))
                                    <time class="block text-sm text-[#B9A572]">{{ $step['date'] }}</time>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const reuploadBtn = document.getElementById('reupload-submit-btn');

            if (reuploadBtn) {
                reuploadBtn.addEventListener('click', function() {
                    const form = document.getElementById('reupload-form');
                    const formData = new FormData(form);

                    const serviceId = {{ $details['id'] }};

                    reuploadBtn.innerHTML = '{{ __('frontend.uploading') }}...';
                    reuploadBtn.disabled = true;

                    fetch(`/vendor/translation-request/${serviceId}/re-upload`, {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                toastr.success(data.message);

                                location.reload();
                            } else {

                                if (data.errors) {
                                    Object.keys(data.errors).forEach(field => {
                                        data.errors[field].forEach(errorMessage => {
                                            toastr.error(errorMessage);
                                        });
                                    });
                                    return;
                                }

                                toastr.error(data.message || '{{ __('frontend.upload_failed') }}');
                            }
                        })
                        .catch(error => {


                            console.error('Error:', error);
                            toastr.error('{{ __('frontend.upload_failed') }}');
                        })
                        .finally(() => {
                            reuploadBtn.innerHTML = '{{ __('frontend.reupload') }}';
                            reuploadBtn.disabled = false;
                        });
                });
            }
        });
    </script>
@endsection
