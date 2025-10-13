@extends('layouts.web_translator', ['title' => 'Service Request Details'])

@section('content')

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

    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium text-gray-900 mb-4">
                {{ __('frontend.recent_consultations') }}
            </h2>
            @if ($details['status'] !== 'completed')
                <button class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-full px-8 py-2 transition"
                    data-modal-target="default-modal" data-modal-toggle="default-modal">
                    {{ __('frontend.update_status') }}
                </button>
            @else
                <button class="bg-gray-400 text-white font-medium rounded-full px-8 py-2 transition cursor-not-allowed"
                    disabled>
                    {{ __('frontend.status_completed_no_change') }}
                </button>
            @endif
        </div>

        <hr class="my-4 border-[#DFDFDF]" />

        <div class="grid grid-cols-2 gap-8">
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
                        {{ date('d M, Y h:i A', strtotime($details['submitted_at'])) }}
                    </p>
                </div>

                @php
                    $status = strtolower($details['status']);
                    $payStatus = strtolower($details['payment_status']);
                @endphp


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
                        <div class="flex ">
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
            </div>

            <div class="pl-8 border-l border-[#DFDFDF] flex flex-col justify-between">
                <div>
                    <div>
                        <span class="font-semibold text-[#23222B]">{{ __('frontend.status') }}</span>
                        <div class="mt-2 mb-6">
                            <span
                                class="{{ $statusClass[$status] ?? '!bg-gray-200 !text-gray-700' }} text-xs font-medium px-5 py-2 rounded-full ml-2">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    </div>

                    <div class="col-span-2">
                        <h3 class="mb-3 font-medium">{{ __('frontend.uploaded_documents') }}:</h3>
                        <div class="space-y-4">
                            @foreach ($details['service_details'] as $key => $files)
                                @if (is_array($files) && !empty($files))
                                    <div>
                                        <p class="text-gray-600 font-medium mb-2">{{ __('frontend.' . $key) }} :
                                        </p>
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
                    </div>
                </div>

                <div class="mt-8">
                    @php $timeline = $details['timeline'] ?? []; @endphp

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
                                        - <span class="text-sm">({{ $step['meta']['rejection_details']['reason'] }})</span>
                                    @endif
                                </h3>
                                @if ($isCompleted && !empty($step['date']))
                                    <time class="block text-sm text-[#B9A572]">{{ $step['date'] }}</time>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="flex justify-end mt-16">
                    @if ($details['status'] === 'completed')
                        <a href="{{ route('translator.service-request.download', ['id' => $details['id']]) }}"
                            class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-10 py-3 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ __('frontend.download') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <div id="default-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">{{ __('frontend.change_status') }}</h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center"
                        data-modal-hide="default-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="bg-white rounded-2xl p-8 w-full">
                    <div class="flex gap-4 mb-6">
                        <select id="status-select"
                            class="border border-[#DFDFDF] rounded-lg px-4 py-3 w-full text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-700">
                            <option value="pending">{{ __('frontend.pending') }}</option>
                            <option value="under_review">{{ __('frontend.under_review') }}</option>
                            <option value="ongoing">{{ __('frontend.ongoing') }}</option>
                            <option value="completed">{{ __('frontend.completed') }}</option>
                            <option value="rejected">{{ __('frontend.rejected') }}</option>
                        </select>
                        <button type="button"
                            class="update-status-btn bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-8 py-3 transition">
                            {{ __('frontend.update') }}
                        </button>
                    </div>

                    <div id="rejected" class="status-section">
                        <div class="mb-2 flex items-center">
                            <input id="supporting-docs" type="checkbox"
                                class="w-5 h-5 border-gray-300 rounded focus:ring-green-700" />
                            <label for="supporting-docs"
                                class="ml-2 text-lg text-[#23222B]">{{ __('frontend.supporting_documents') }}</label>
                        </div>
                        <div class="mb-6 flex items-center">
                            <input id="supporting-docs-any" type="checkbox" checked
                                class="w-5 h-5 border-gray-300 rounded focus:ring-green-700" />
                            <label for="supporting-docs-any"
                                class="ml-2 text-lg text-[#23222B]">{{ __('frontend.supporting_documents_any') }}</label>
                        </div>
                        <label for="case-type"
                            class="block text-sm font-medium text-gray-700 mb-2 block">{{ __('frontend.reason') }}</label>
                        <textarea id="reason" rows="4"
                            class="w-full border border-[#DFDFDF] rounded-lg px-4 py-3 mb-8 focus:outline-none focus:ring-2 focus:ring-green-700"
                            placeholder="{{ __('frontend.type_here') }}"></textarea>
                        <button type="button"
                            class="update-status-btn bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-10 py-3 transition">
                            {{ __('frontend.update') }}
                        </button>
                    </div>

                    <div id="completed" class="status-section hidden">
                        <label for="case-type"
                            class="block text-sm font-medium text-gray-700 mb-2 block">{{ __('frontend.upload_files') }}</label>
                        <div class="flex flex-col gap-4 mb-6">
                            <input
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                id="file_input" type="file" />
                            <button type="button"
                                class="update-status-btn bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-8 py-3 transition">
                                {{ __('frontend.update') }}
                            </button>
                        </div>
                    </div>

                    <div id="pending" class="status-section hidden">
                        <p class="text-gray-700">{{ __('frontend.task_pending') }}</p>
                    </div>

                    <div id="under_review" class="status-section hidden">
                        <p class="text-gray-700">{{ __('frontend.task_under_review') }}</p>
                    </div>

                    <div id="ongoing" class="status-section hidden">
                        <p class="text-gray-700">{{ __('frontend.task_ongoing') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const selectEl = document.getElementById("status-select");
        const sections = document.querySelectorAll(".status-section");
        const serviceRequestId = {{ $details['id'] }};

        function showSelectedSection() {
            const selected = selectEl.value;

            sections.forEach((section) => {
                section.classList.add("hidden");
            });

            const selectedSection = document.getElementById(selected);
            if (selectedSection) {
                selectedSection.classList.remove("hidden");
            }
        }

        function clearAllFields() {
            const supportingDocs = document.getElementById('supporting-docs');
            const supportingDocsAny = document.getElementById('supporting-docs-any');
            const reasonTextarea = document.getElementById('reason');

            if (supportingDocs) supportingDocs.checked = false;
            if (supportingDocsAny) supportingDocsAny.checked = false;
            if (reasonTextarea) reasonTextarea.value = '';

            const fileInput = document.getElementById('file_input');
            if (fileInput) fileInput.value = '';

            if (selectEl) selectEl.selectedIndex = 0;
        }

        selectEl.addEventListener("change", showSelectedSection);

        document.addEventListener("DOMContentLoaded", showSelectedSection);

        const currentStatus = "{{ $details['status'] }}";

        document.querySelectorAll('.update-status-btn').forEach(button => {
            button.addEventListener('click', function() {
                const status = document.getElementById('status-select').value;

                if (currentStatus === 'completed' && status !== 'completed') {
                    toastr.error('{{ __('frontend.cannot_change_completed_status') }}');
                    return;
                }

                if (status === currentStatus) {
                    toastr.error('{{ __('frontend.same_status_error') }}');
                    return;
                }

                const formData = new FormData();
                formData.append('status', status);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'));

                if (status === 'rejected') {
                    const reasonElement = document.getElementById('reason');
                    if (reasonElement) {
                        formData.append('reason', reasonElement.value);
                    }

                    const supportingDocs = document.getElementById('supporting-docs');
                    const supportingDocsAny = document.getElementById('supporting-docs-any');
                    formData.append('supporting_docs', supportingDocs ? supportingDocs.checked : false);
                    formData.append('supporting_docs_any', supportingDocsAny ? supportingDocsAny.checked :
                        false);
                }

                if (status === 'completed') {
                    const fileInput = document.getElementById('file_input');
                    if (fileInput && fileInput.files[0]) {
                        formData.append('file', fileInput.files[0]);
                    } else {
                        toastr.error('{{ __('frontend.please_select_file_to_upload') }}');
                        return;
                    }
                }

                const originalText = this.textContent;
                this.textContent = '{{ __('frontend.updating') }}';
                this.disabled = true;

                fetch(`/translator/service-request/${serviceRequestId}/update-status`, {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            const statusBadge = document.getElementById('status-badge');
                            if (statusBadge) {
                                statusBadge.textContent = data.new_status;
                            }

                            toastr.success('{{ __('frontend.updated_successfully') }}');

                            const modal = document.getElementById('default-modal');
                            modal.classList.add('hidden');

                            clearAllFields();

                            window.location.reload();
                        } else {
                            if (data.errors) {
                                Object.keys(data.errors).forEach(field => {
                                    data.errors[field].forEach(errorMessage => {
                                        toastr.error(errorMessage);
                                    });
                                });
                                return;
                            }

                            toastr.error(data.message || '{{ __('frontend.failed_to_update') }}');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('{{ __('frontend.failed_to_update') }}: ' + error.message);
                    })
                    .finally(() => {
                        this.textContent = originalText;
                        this.disabled = false;
                    });
            });
        });
    </script>
@endsection
