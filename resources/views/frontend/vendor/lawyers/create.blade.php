@extends('layouts.web_vendor_default', ['title' => __('frontend.create_lawyer')])

@section('content')
<div class="bg-white rounded-2xl  p-8 pb-12">

    <div class="flex justify-between items-center mb-8">
        <h2 class="text-xl font-semibold text-gray-800">Lawyer Profile</h2>
    </div>
    <div class="mx-auto mt-10">
        <!-- Stepper -->
        <div class="relative mb-8">
            <div class="flex justify-between items-center">
                <div class="step flex flex-col items-center z-[999]" data-step="0">
                    <div
                        class="circle w-8 h-8 flex items-center justify-center relative  rounded-full bg-blue-600 text-white">
                        1</div>
                    <span class="text-sm mt-2 z-[999]">Step 1</span>
                </div>
                <div class="step flex flex-col items-center z-[999]" data-step="1">
                    <div class="circle w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-800">
                        2</div>
                    <span class="text-sm mt-2">Step 2</span>
                </div>
                <div class="step flex flex-col items-center z-[999]" data-step="2">
                    <div class="circle w-8 h-8 flex items-center justify-center rounded-full bg-gray-300 text-gray-800">
                        3</div>
                    <span class="text-sm mt-2">Step 3</span>
                </div>
            </div>
            <!-- Progress Line -->
            <div class="absolute top-4 left-2 w-[99%] h-1 bg-gray-200 z-0">
                <div id="progress-bar" class="h-1 bg-blue-600 w-0 transition-all duration-300 z-[-1] relative">
                </div>
            </div>
        </div>

        <!-- Step Content -->
        <div class="bg-white border p-4 rounded-md">
            <div id="step-0" class="step-content">You are on <strong>Step 1</strong></div>
            <div id="step-1" class="step-content hidden">This is <strong>Step 2</strong></div>
            <div id="step-2" class="step-content hidden">This is <strong>Step 3</strong></div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-4">
            <button id="prevBtn" class="px-4 py-2 text-sm text-white bg-gray-500 rounded disabled:opacity-50"
                disabled>Previous</button>
            <button id="nextBtn" class="px-4 py-2 text-sm text-white bg-blue-600 rounded">Next</button>
        </div>
    </div>
</div>
@endsection