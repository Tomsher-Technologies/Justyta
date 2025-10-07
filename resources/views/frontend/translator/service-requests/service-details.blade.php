@extends('layouts.web_translator', ['title' => 'Service Request Details'])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium text-gray-900 mb-4">
                Recent Consultations
            </h2>
            <button class="bg-green-700 hover:bg-green-800 text-white font-medium rounded-full px-8 py-2 transition"
                data-modal-target="default-modal" data-modal-toggle="default-modal">
                Update Status
            </button>
        </div>

        <hr class="my-4 border-[#DFDFDF]" />

        <div class="grid grid-cols-2 gap-8">
            <div class="space-y-6 text-[18px]">
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Ref. No :</span>
                    <span class="font-medium">REF-001234</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Document Title :</span>
                    <span class="font-medium">Legal Experts</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Type Of Document :</span>
                    <span class="font-medium">Legal Proposal</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Sub Document Type :</span>
                    <span class="font-medium">Trade License</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">No. Of Pages :</span>
                    <span class="font-medium">20</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Date and Time :</span>
                    <span class="font-medium">2025-05-21 10:30 AM</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Document Language :</span>
                    <span class="font-medium">English</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Translation Language :</span>
                    <span class="font-medium">Arabic</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Priority Level :</span>
                    <span class="font-medium">Normal</span>
                </div>
                <div class="flex">
                    <span class="w-56 text-[#23222B]">Receive by :</span>
                    <span class="font-medium">Email</span>
                </div>
            </div>

            <div class="pl-8 border-l border-[#DFDFDF] flex flex-col justify-between">
                <div>
                    <div>
                        <span class="font-semibold text-[#23222B]">Status</span>
                        <div class="mt-2 mb-6">
                            <span class="bg-[#EDE5CF] text-[#B9A572] rounded-full px-6 py-2 font-medium text-base">In
                                Progress</span>
                        </div>
                    </div>
                    <div class="mb-6">
                        <span class="font-semibold text-[#23222B]">Documents :</span>
                        <div class="flex gap-3 mt-2">
                            <a href="#"
                                class="flex items-center bg-[#F6F2F0] border rounded px-4 py-2 text-[#4D1717] font-medium text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke="#4D1717" stroke-width="2"
                                        d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V8.828a2 2 0 00-.586-1.414l-4.828-4.828A2 2 0 0012.172 2H6a2 2 0 00-2 2v16a2 2 0 002 2z" />
                                </svg>
                                document-title.pdf
                            </a>
                            <a href="#"
                                class="flex items-center bg-[#F6F2F0] border rounded px-4 py-2 text-[#4D1717] font-medium text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke="#4D1717" stroke-width="2"
                                        d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V8.828a2 2 0 00-.586-1.414l-4.828-4.828A2 2 0 0012.172 2H6a2 2 0 00-2 2v16a2 2 0 002 2z" />
                                </svg>
                                document-title.pdf
                            </a>
                        </div>
                    </div>
                    <div>
                        <span class="font-semibold text-[#23222B]">Trade License :</span>
                        <div class="flex gap-3 mt-2">
                            <a href="#"
                                class="flex items-center bg-[#F6F2F0] border rounded px-4 py-2 text-[#4D1717] font-medium text-base">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke="#4D1717" stroke-width="2"
                                        d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V8.828a2 2 0 00-.586-1.414l-4.828-4.828A2 2 0 0012.172 2H6a2 2 0 00-2 2v16a2 2 0 002 2z" />
                                </svg>
                                document-title.pdf
                            </a>
                        </div>
                    </div>
                </div>
                <div class="mt-8">
                    <ol class="relative border-l-2 border-[#DFDFDF] ml-3">
                        <li class="mb-8 ml-6">
                            <span
                                class="absolute -left-3 flex items-center justify-center w-4 h-4 bg-[#EDE5CF] rounded-full border-2 border-[#C7B07A]"></span>
                            <h3 class="font-semibold text-[#B9A572] text-lg">
                                Submitted
                            </h3>
                            <time class="block text-sm text-[#B9A572]">May 25, 2025</time>
                        </li>
                        <li class="mb-8 ml-6">
                            <span
                                class="absolute -left-3 flex items-center justify-center w-4 h-4 bg-[#EDE5CF] rounded-full border-2 border-[#C7B07A]"></span>
                            <h3 class="font-semibold text-[#B9A572] text-lg">
                                Under Review
                            </h3>
                            <time class="block text-sm text-[#B9A572]">May 24, 2025</time>
                        </li>
                        <li class="mb-8 ml-6">
                            <span
                                class="absolute -left-3 flex items-center justify-center w-4 h-4 bg-[#EDE5CF] rounded-full border-2 border-[#C7B07A]"></span>
                            <h3 class="font-semibold text-[#B9A572] text-lg">
                                Rejected
                            </h3>
                            <time class="block text-sm text-[#B9A572]">May 23, 2025</time>
                        </li>
                        <li class="mb-8 ml-6">
                            <span
                                class="absolute -left-3 flex items-center justify-center w-4 h-4 bg-[#EDE5CF] rounded-full border-2 border-[#C7B07A]"></span>
                            <h3 class="font-semibold text-[#B9A572] text-lg">
                                In Progress
                            </h3>
                            <time class="block text-sm text-[#B9A572]">May 22, 2025</time>
                        </li>
                        <li class="ml-6">
                            <span
                                class="absolute -left-3 flex items-center justify-center w-4 h-4 bg-[#DFDFDF] rounded-full border-2 border-[#DFDFDF]"></span>
                            <h3 class="font-semibold text-[#C7C7C7] text-lg">
                                Completed
                            </h3>
                        </li>
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
@endsection
