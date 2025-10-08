@extends('layouts.web_translator', ['title' => 'Service Request Details'])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium text-gray-900 mb-4">
                {{ __('frontend.recent_consultations') }}
            </h2>
            <button class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-full px-8 py-2 transition"
                data-modal-target="default-modal" data-modal-toggle="default-modal">
                {{ __('frontend.update_status') }}
            </button>
        </div>

        <hr class="my-4 border-[#DFDFDF]" />

        <div class="grid grid-cols-2 gap-8">
            <div class="space-y-6 text-[18px]">
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.ref_no') }} :</span>
                    <span class="font-medium">{{ $details['reference_code'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.document_title') }} :</span>
                    <span class="font-medium">{{ $details['document_title'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.type_of_document') }} :</span>
                    <span class="font-medium">{{ $details['document_type'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.sub_document_type') }} :</span>
                    <span class="font-medium">{{ $details['sub_document_type'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.no_of_page') }} :</span>
                    <span class="font-medium">{{ $details['no_of_pages'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.date_time') }} :</span>
                    <span class="font-medium">{{ $details['created_at'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.document_language') }} :</span>
                    <span class="font-medium">{{ $details['document_language'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.translation_language') }} :</span>
                    <span class="font-medium">{{ $details['translation_language'] }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.priority_level') }} :</span>
                    <span class="font-medium">{{ ucfirst($details['priority']) }}</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">{{ __('frontend.receive_by') }} :</span>
                    <span class="font-medium">{{ $details['receive_by'] }}</span>
                </div>
            </div>

            <div class="pl-8 border-l border-[#DFDFDF] flex flex-col justify-between">
                <div>
                    <div>
                        <span class="font-semibold text-[#23222B]">{{ __('frontend.status') }}</span>
                        <div class="mt-2 mb-6">
                            <span id="status-badge"
                                class="bg-[#EDE5CF] text-[#B9A572] rounded-full px-6 py-2 font-medium text-base">{{ ucfirst($details['status']) }}</span>
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
                                $isCompleted = $step['completed'];
                                $dotClasses = $isCompleted
                                    ? 'bg-[#EDE5CF] border-[#C7B07A]'
                                    : 'bg-[#DFDFDF] border-[#DFDFDF]';
                                $textClasses = $isCompleted ? 'text-[#B9A572]' : 'text-[#C7C7C7]';
                            @endphp

                            <li class="mb-8 ml-6">
                                <span
                                    class="absolute -left-3 flex items-center justify-center w-4 h-4 rounded-full border-2 {{ $dotClasses }}"></span>
                                <h3 class="font-semibold {{ $textClasses }} text-lg">
                                    {{ $step['label'] }}
                                </h3>
                                @if ($isCompleted && !empty($step['date']))
                                    <time class="block text-sm text-[#B9A572]">{{ $step['date'] }}</time>
                                @endif
                            </li>
                        @endforeach
                    </ol>

                </div>
                <div class="flex justify-end mt-16">
                    <button class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-10 py-3 transition">
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div id="default-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Change Status</h3>
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
                <!-- Modal body -->
                <div class="bg-white rounded-2xl p-8 w-full">
                    <div class="flex gap-4 mb-6">
                        <select id="status-select"
                            class="border border-[#DFDFDF] rounded-lg px-4 py-3 w-full text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-700">
                            <option value="pending">Pending</option>
                            <option value="under_review">Under Review</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                        <button type="button"
                            class="update-status-btn bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-8 py-3 transition">
                            Update
                        </button>
                    </div>

                    <div id="rejected" class="status-section">
                        <div class="mb-2 flex items-center">
                            <input id="supporting-docs" type="checkbox"
                                class="w-5 h-5 border-gray-300 rounded focus:ring-green-700" />
                            <label for="supporting-docs" class="ml-2 text-lg text-[#23222B]">Supporting Documents</label>
                        </div>
                        <div class="mb-6 flex items-center">
                            <input id="supporting-docs-any" type="checkbox" checked
                                class="w-5 h-5 border-gray-300 rounded focus:ring-green-700" />
                            <label for="supporting-docs-any" class="ml-2 text-lg text-[#23222B]">Supporting Documents if
                                any</label>
                        </div>
                        <label for="case-type" class="block text-sm font-medium text-gray-700 mb-2 block">Reason</label>
                        <textarea id="reason" rows="4"
                            class="w-full border border-[#DFDFDF] rounded-lg px-4 py-3 mb-8 focus:outline-none focus:ring-2 focus:ring-green-700"
                            placeholder="Type here..."></textarea>
                        <button type="button"
                            class="update-status-btn bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-10 py-3 transition">
                            Update
                        </button>
                    </div>

                    <div id="completed" class="status-section hidden">
                        <label for="case-type" class="block text-sm font-medium text-gray-700 mb-2 block">Upload
                            Files</label>
                        <div class="flex gap-4 mb-6">
                            <input
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none"
                                id="file_input" type="file" />
                            <button type="button"
                                class="update-status-btn bg-green-700 hover:bg-green-800 text-white font-medium rounded-lg px-8 py-3 transition">
                                Update
                            </button>
                        </div>
                    </div>

                    <div id="pending" class="status-section hidden">
                        <p class="text-gray-700">Task is pending.</p>
                    </div>

                    <div id="under_review" class="status-section hidden">
                        <p class="text-gray-700">Task is under review.</p>
                    </div>

                    <div id="ongoing" class="status-section hidden">
                        <p class="text-gray-700">Task is currently ongoing.</p>
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

        selectEl.addEventListener("change", showSelectedSection);
        
        document.addEventListener("DOMContentLoaded", showSelectedSection);

        document.querySelectorAll('.update-status-btn').forEach(button => {
            button.addEventListener('click', function() {
                const status = document.getElementById('status-select').value;
                let reason = '';

                if (status === 'rejected') {
                    const reasonElement = document.getElementById('reason');
                    if (reasonElement) {
                        reason = reasonElement.value;
                    }
                }

                const originalText = this.textContent;
                this.textContent = 'Updating...';
                this.disabled = true;

                fetch(`/translator/service-request/${serviceRequestId}/update-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status: status,
                            reason: reason
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("data",data);
                        
                        if (data.success) {
                            const statusBadge = document.getElementById('status-badge');
                            if(statusBadge) {
                                statusBadge.textContent = data.new_status;
                            }

                            alert('Status updated successfully!');

                            const modal = document.getElementById('default-modal');
                            modal.classList.add('hidden');

                            location.reload();
                        } else {
                            alert('Error: ' + (data.message || 'Failed to update status'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while updating the status: ' + error.message);
                    })
                    .finally(() => {
                        this.textContent = originalText;
                        this.disabled = false;
                    });
            });
        });
    </script>
@endsection
