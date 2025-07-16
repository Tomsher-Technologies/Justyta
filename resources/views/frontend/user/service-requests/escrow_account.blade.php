@extends('layouts.web_default', ['title' => $service->getTranslation('title', $lang)])

@section('content')
    <form method="POST" action="{{ route('service.escrow-account-request') }}" id="escrowAccountForm" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">
                    {{ $service->getTranslation('title', $lang) }}
                </h2>
                <hr class="mb-5" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                        <div class="border-b pb-6 col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('frontend.applicant_type')  }}<span class="text-red-500">*</span></label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input id="applicant-company" type="radio" value="company" name="applicant_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500" {{ (old('applicant_type', 'company') == 'company') ? 'checked' : '' }} />
                                <label for="applicant-company" class="ms-2 text-sm text-gray-900">{{ __('frontend.company')  }}</label>
                            </div>
                            <div class="flex items-center">
                                <input id="applicant-individual" type="radio" value="individual" name="applicant_type" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500"  {{ (old('applicant_type') == 'individual') ? 'checked' : '' }}/>
                                <label for="applicant-individual" class="ms-2 text-sm text-gray-900">{{ __('frontend.individual')  }}</label>
                            </div>
                        </div>
                        @error('applicant_type')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="emirate" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.company_name') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter_company_name') }}" value="{{ old('company_name') }}" name="company_name" id="company_name">
                        @error('company_name')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="company_activity" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.company_activity') }}<span class="text-red-500">*</span></label>
                        <select id="company_activity" name="company_activity" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['industries'] as $casetype)
                                <option value="{{ $casetype['id'] }}"  {{ (old('company_activity') == $casetype['id']) ? 'selected' : '' }}>{{ $casetype['value'] }}</option>
                            @endforeach
                        </select>
                        @error('company_activity')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="company_origin" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.company_origin') }}<span class="text-red-500">*</span></label>
                        <select id="company_origin" name="company_origin" class="select2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                            <option value="">{{ __('frontend.choose_option') }}</option>
                            @foreach ($dropdownData['company_origin'] as $casetype)
                                <option value="{{ $casetype['id'] }}"  {{ (old('company_origin') == $casetype['id']) ? 'selected' : '' }}>{{ $casetype['value'] }}</option>
                            @endforeach
                        </select>
                        @error('company_origin')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="preferred-language" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.amount') }}<span class="text-red-500">*</span></label>
                        <input type="text" class="bg-[#F9F9F9] border border-gray-300 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.enter_amount') }}" name="amount" id="amount" value="{{ old('amount') }}">
                        @error('amount')
                            <span class="text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-2">
                        <label for="you-represent" class="block text-sm font-medium text-gray-700 mb-2">{{ __('frontend.about_deal') }}<span class="text-red-500">*</span></label>
                        <textarea id="about_deal" name="about_deal" rows="11" class="bg-[#F9F9F9] border border-gray-300 mb-1 text-gray-900 text-sm rounded-[10px] focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" placeholder="{{ __('frontend.type_here') }}">{{ old('about_deal') }}</textarea>
                        <span class="text-[#717171] text-sm">0/1000</span>
                    </div>
                    
        
                </div>

                {{-- <hr class="my-8 mb-5" /> --}}

            </div>
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-10 rounded-[20px] border !border-[#FFE9B1] h-[calc(100vh-150px)] flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            {{ __('frontend.description') }}
                        </h2>

                        <hr class="my-5" />
                        <p class="text-gray-600 text-sm leading-relaxed">
                            {!! $service->getTranslation('description', $lang) !!}
                        </p>
                    </div>

                    <div>
                        <button type="submit" class="text-white bg-[#04502E] hover:bg-[#02331D] focus:ring-4 focus:ring-blue-300 font-normal rounded-xl text-md w-full px-8 py-4 text-center transition-colors duration-200 uppercase cursor-pointer">
                            {{ __('frontend.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('script')
    <!-- Load jQuery Validate -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

    <script>
       
        $(document).ready(function () {
           
            $("#escrowAccountForm").validate({
                ignore: [],
                rules: {
                    applicant_type: { required: true },
                    company_name: { required: true },
                    company_origin: { required: true },
                    company_activity: { required: true },
                    amount: { required: true },
                    about_deal: { required: true },
                },
                messages: {
                    applicant_type: "{{ __('messages.applicant_type_required') }}",
                    company_name: "{{ __('messages.company_name_required') }}",
                    company_origin: "{{ __('messages.company_origin_required') }}",
                    company_activity: "{{ __('messages.company_activity_required') }}",
                    amount: "{{ __('messages.amount_required') }}",
                    about_deal: "{{ __('messages.about_deal_required') }}",
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('text-red-500 text-sm');

                    if (element.hasClass('select2-hidden-accessible')) {
                        error.insertAfter(element.next('.select2')); // Insert after the visible Select2 dropdown
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2').find('.select2-selection')
                            .addClass('border-red-500');
                    } else {
                        $(element).addClass('border-red-500');
                    }
                },
                unhighlight: function (element) {
                    if ($(element).hasClass('select2-hidden-accessible')) {
                        $(element).next('.select2').find('.select2-selection')
                            .removeClass('border-red-500');
                    } else {
                        $(element).removeClass('border-red-500');
                    }
                },

                /** 👇 Prevent actual form submission if invalid */
                submitHandler: function (form) {
                    form.submit(); // real submit
                }
            });

          
        });
    </script>
@endsection