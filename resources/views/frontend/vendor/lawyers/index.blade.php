@extends('layouts.web_vendor_default', ['title' => __('frontend.lawyers')])

@section('content')
<div class="bg-white rounded-2xl  p-8 pb-12">
    <div class="flex justify-between items-center mb-8 border-b pb-5">
        <h2 class="text-xl font-semibold text-gray-800">{{ __('frontend.lawyers') }}</h2>

        @if (isVendorCanCreateLawyers())
            <a href="{{ route('vendor.create.lawyers') }}" class="text-white bg-[#07683B] rounded-full py-2.5 px-6">
                {{ __('frontend.create_lawyer') }}
            </a>
        @endif
    </div>
    <form method="GET" id="filterForm" action="{{ route('vendor.lawyers') }}" autocomplete="off">
        <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4 mb-8">
            <div class="relative col-span-6">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" id="simple-search"  name="keyword" value="{{ request()->keyword ?? ''}}"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3.5"
                    placeholder="{{ __('frontend.search_name_email_phone') }}" />
            </div>
            <div class="col-span-4">
                <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.specialities') }}</label>
                <select name="specialities" id="select-tag" class="form-control select2 ip-gray radius-xs b-light px-15">
                    <option value="">{{ __('frontend.select_speciality') }}</option>
                    @foreach($dropdowns['specialities']->options as $option)
                        <option value="{{ $option->id }}" {{ ($option->id == request()->specialities ) ? 'selected' : '' }}>
                            {{ $option->getTranslation('name', app()->getLocale()) ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label for="status" class="block mb-2 text-sm font-medium text-gray-900">{{ __('frontend.status') }}</label>
                <select  name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                    <option value="">{{ __('frontend.all') }}</option>
                    <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>{{ __('frontend.active') }} </option>
                    <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>{{ __('frontend.inactive') }} </option>
                </select>
            </div>
        </div>
    </form>
    @if ($lawyers->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($lawyers as $key => $lawyer)
                <div class="{{ $lawyer->user->banned == 1 ? 'bg-gray-100 opacity-50' : 'bg-white' }} rounded-lg border border-[#DDD3B9] p-6 relative">
                    <a href="{{ route('vendor.edit.lawyers', base64_encode($lawyer->id)) }}" 
                    class="absolute top-3 right-3 text-blue-600 hover:underline text-sm">
                        <svg class="w-6 h-6 text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" 
                            stroke-width="2"
                            d="M09 7H4a2 3 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-06M16.5 3.5a2.121 2.1 0 013 3L10 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </a>

                    <a href="{{ route('vendor.view.lawyers', base64_encode($lawyer->id)) }}">
                        <div class="flex items-middle gap-6 w-full">
                            <div class="relative inline-block">
                                <img class="h-[130px] w-[130px] rounded-full object-cover"
                                    src="{{ asset(getUploadedUserImage($lawyer->profile_photo)) }}" alt="{{ $lawyer->getTranslation('full_name', app()->getLocale()) }}">
                                
                                @if($lawyer->user->is_online)
                                    <span class="absolute bottom-2 right-2 w-5 h-5 bg-green-500 border-2 border-white rounded-full" title="Online"></span>
                                @else
                                    <span class="absolute bottom-2 right-2 w-5 h-5 bg-gray-400 border-2 border-white rounded-full" title="Offline"></span>
                                @endif
                            </div>
                            <div>
                                <div class="border-b pb-4 mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $lawyer->getTranslation('full_name', app()->getLocale()) }}</h3>
                                    <p class="text-sm text-gray-500">{{ $lawyer->ref_no }}</p>
                                </div>
                                <div class="text-sm text-gray-700">
                                    <p>{{ __('frontend.last_login') }} : <span class="font-medium">
                                        {{ ($lawyer->user?->last_login_at != null) ? date('d M Y, h:i A', strtotime($lawyer->user?->last_login_at)) : '' }}
                                    </span></p>
                                    <p>{{ __('frontend.no_of_consultation') }} : <span class="font-medium">0</span></p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            @endforeach
        </div>
    @else
        <div>
            <div class="border-b pb-4 mt-4 mb-4 w-full text-center">
                <h3 class="text-md font-semibold text-gray-900">{{ __('frontend.no_data_found') }}</h3>
            </div>
        </div>
    @endif

    <div class="mt-6">
        {{ $lawyers->links() }}
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filterForm');

        filterForm.querySelectorAll('select').forEach(function (el) {
            el.addEventListener('change', function () {
                filterForm.submit();
            });
        });

        $('#select-tag').select2().on('change', function() {
            filterForm.submit();
        });

        let typingTimer;
        const keywordInput = document.getElementById('simple-search');
        keywordInput.addEventListener('keyup', function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                filterForm.submit();
            }, 500); 
        });
    });
</script>
@endsection