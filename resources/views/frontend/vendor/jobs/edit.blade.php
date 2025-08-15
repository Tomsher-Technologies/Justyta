@extends('layouts.web_vendor_default', ['title' => __('frontend.edit_job')])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <h2 class="text-xl font-medium text-gray-900">{{ __('frontend.edit_job') }}</h2>
        <hr class="my-4 border-[#DFDFDF]">

        <form class="grid grid-cols-1 md:grid-cols-4 gap-4" autocomplete="off" action="{{ route('jobs.update', base64_encode($jobPost->id)) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.emirate') }} <span class="text-red-500">*</span></label>
                <select name="emirate" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('frontend.choose_option') }}</option>
                    @foreach (\App\Models\Emirate::with('translations')->get() as $emirate)
                        <option value="{{ $emirate->id }}" {{ old('emirate',$jobPost->emirate) == $emirate->id ? 'selected' : '' }}>
                            {{ $emirate->translation('en')?->name }}
                        </option>
                    @endforeach
                </select>
                @error('emirate')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.job_type') }} <span class="text-red-500">*</span></label>
                <select name="type"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('frontend.choose_option') }}</option>
                    <option value="full_time" {{ old('type', $jobPost->type) == 'full_time' ? 'selected' : '' }}>{{ __('frontend.full_time') }}</option>
                    <option value="part_time" {{ old('type', $jobPost->type) == 'part_time' ? 'selected' : '' }}>{{ __('frontend.part_time') }}</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">{{ __('frontend.deadline_date') }} <span
                        class="text-red-500">*</span></label>
                <input type="date" name="deadline_date"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 datepicker"
                    value="{{ old('deadline_date', $jobPost->deadline_date ?? '') }}">
                @error('deadline_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-1 md:col-span-4 mt-6" x-data="{ activeTab: '{{ $languages->first()->code }}' }">

                <!-- Tabs -->
                <div class="flex flex-wrap border-b border-gray-300 bg-gray-50 rounded-t-lg">
                    @foreach ($languages as $lang)
                        <button type="button" @click="activeTab = '{{ $lang->code }}'"
                            :class="activeTab === '{{ $lang->code }}'
                                ?
                                'text-indigo-600 border-b-2 border-indigo-500 bg-white' :
                                'text-gray-500 hover:text-indigo-500 hover:bg-gray-100'"
                            class="px-6 py-3 text-sm font-medium focus:outline-none transition-all duration-200">
                            {{ $lang->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Tab Contents -->
                <div class="bg-white shadow-md border border-gray-200 rounded-b-lg p-6">
                    @foreach ($languages as $lang)
                        @php
                            $trans = $jobPost->translations->firstWhere('lang', $lang->code);
                        @endphp
                        <div x-show="activeTab === '{{ $lang->code }}'" x-cloak class="space-y-5">

                            <!-- Title -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    {{ __('frontend.title') }} ({{ $lang->name }})
                                    @if ($lang->code == 'en')
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input type="text" @if ($lang->rtl) dir="rtl" @endif
                                    name="translations[{{ $lang->code }}][title]"
                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2.5"
                                    value="{{ old('translations.' . $lang->code . '.title',  $trans->title ?? '') }}">
                                @error("translations.$lang->code.title")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">
                                    {{ __('frontend.description') }} ({{ $lang->name }})
                                    @if ($lang->code == 'en')
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <textarea name="translations[{{ $lang->code }}][description]" @if ($lang->rtl) dir="rtl" @endif
                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2.5 tinymce-editor"
                                    rows="6">{{ old('translations.' . $lang->code . '.description', $trans->description ?? '') }}</textarea>
                                @error("translations.$lang->code.description")
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Salary -->
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">{{ __('frontend.salary') }} ({{ $lang->name }})</label>
                                <input type="text" name="translations[{{ $lang->code }}][salary]"
                                    @if ($lang->rtl) dir="rtl" @endif
                                    class="w-full border border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-2.5"
                                    value="{{ old('translations.' . $lang->code . '.salary',  $trans->salary ?? '') }}">
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-span-1 md:col-span-4 flex justify-end gap-2 mt-4">
                <button type="submit"
                    class="bg-[#07683B] text-white px-4 py-2 rounded-md shadow hover:bg-[#07683B]">{{ __('frontend.submit') }}</button>
                <a href="{{ Session::has('jobs_last_url') ? Session::get('jobs_last_url') : route('jobs.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-md shadow hover:bg-gray-600">{{ __('frontend.cancel') }}</a>
            </div>
        </form>


    </div>
@endsection
