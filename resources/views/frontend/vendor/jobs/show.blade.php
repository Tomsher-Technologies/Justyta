@extends('layouts.web_vendor_default', ['title' => __('frontend.add_job')])

@section('content')

<div class="grid grid-cols-1 gap-6">
    <div class=" bg-white p-10 rounded-[20px] border !border-[#FFE9B1]">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-medium mb-2">{{ $jobPost['title'] }}</h2>
                <span
                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">{{ $jobPost['type'] }}</span>
            </div>
            <div>
               
                <a href="{{ Session::has('jobs_last_url') ? Session::get('jobs_last_url') : route('jobs.index') }}"
                    class="inline-flex items-center px-4 py-2 text-black bg-[#c4b07e] hover:bg-[#c4b07e]-800 focus:ring-4 focus:ring-green-300 font-medium rounded-full text-base dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                    {{ __('frontend.go_back') }}
                    <svg class="w-4 h-4 ms-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10" aria-hidden="true">
                        <path stroke="black" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 5H1m0 0l4-4M1 5l4 4" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <div class="lg:w-2/3  break-words">
                <h2 class="text-xl font-semibold mb-3">{{ __('frontend.job_description') }}</h2>
                {!! $jobPost['description'] !!}
            </div>

            <div class="lg:w-1/3 space-y-6">
                <div class="bg-white border border-gray-200 rounded-lg p-6 grid grid-cols-2 gap-5 items-center">
                    <div class="mb-2 text-center">
                        <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="39" height="39" fill="rgba(185,165,114,1)"><path d="M17.0047 16.0028H19.0047V4.00281H9.00468V6.00281H17.0047V16.0028ZM17.0047 18.0028V21.0019C17.0047 21.5547 16.5547 22.0028 15.9978 22.0028H4.01154C3.45548 22.0028 3.00488 21.5582 3.00488 21.0019L3.00748 7.00368C3.00759 6.45091 3.45752 6.00281 4.0143 6.00281H7.00468V3.00281C7.00468 2.45052 7.4524 2.00281 8.00468 2.00281H20.0047C20.557 2.00281 21.0047 2.45052 21.0047 3.00281V17.0028C21.0047 17.5551 20.557 18.0028 20.0047 18.0028H17.0047ZM5.0073 8.00281L5.00507 20.0028H15.0047V8.00281H5.0073ZM7.00468 16.0028H11.5047C11.7808 16.0028 12.0047 15.7789 12.0047 15.5028C12.0047 15.2267 11.7808 15.0028 11.5047 15.0028H8.50468C7.12397 15.0028 6.00468 13.8835 6.00468 12.5028C6.00468 11.1221 7.12397 10.0028 8.50468 10.0028H9.00468V9.00281H11.0047V10.0028H13.0047V12.0028H8.50468C8.22854 12.0028 8.00468 12.2267 8.00468 12.5028C8.00468 12.7789 8.22854 13.0028 8.50468 13.0028H11.5047C12.8854 13.0028 14.0047 14.1221 14.0047 15.5028C14.0047 16.8835 12.8854 18.0028 11.5047 18.0028H11.0047V19.0028H9.00468V18.0028H7.00468V16.0028Z"></path></svg>
                        <p class="text-gray-600 font-semibold my-2 text-[16px] text-black">{{ __('frontend.salary_range') }}</p>
                        <p class="text-md font-medium text-[#07683B] my-2 break-words">{{ $jobPost['salary'] }}</p>
                        {{-- <p class="text-sm text-gray-500">Yearly salary</p> --}}
                    </div>


                    <div class="mb-2 text-center">
                        <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="rgba(185,165,114,1)"><path d="M12 20.8995L16.9497 15.9497C19.6834 13.2161 19.6834 8.78392 16.9497 6.05025C14.2161 3.31658 9.78392 3.31658 7.05025 6.05025C4.31658 8.78392 4.31658 13.2161 7.05025 15.9497L12 20.8995ZM12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.15076 5.63604 4.63604C9.15076 1.12132 14.8492 1.12132 18.364 4.63604C21.8787 8.15076 21.8787 13.8492 18.364 17.364L12 23.7279ZM12 13C13.1046 13 14 12.1046 14 11C14 9.89543 13.1046 9 12 9C10.8954 9 10 9.89543 10 11C10 12.1046 10.8954 13 12 13ZM12 15C9.79086 15 8 13.2091 8 11C8 8.79086 9.79086 7 12 7C14.2091 7 16 8.79086 16 11C16 13.2091 14.2091 15 12 15Z"></path></svg>
                        <p class="text-gray-600 font-semibold my-2 text-[16px] text-black">{{ __('frontend.job_location') }}</p>
                        <p class="text-md mb-0 font-semibold text-[#767F8C] break-words">{{ $jobPost['location'] }}</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-lg p-5">
                    <h3 class="text-lg font-medium mb-4">{{ __('frontend.job_overview') }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-gray-700 text-sm ">
                        <div class="mb-2 text-center">
                            <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="rgba(185,165,114,1)"><path d="M9 1V3H15V1H17V3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3H7V1H9ZM20 10H4V19H20V10ZM15.0355 11.136L16.4497 12.5503L11.5 17.5L7.96447 13.9645L9.37868 12.5503L11.5 14.6716L15.0355 11.136ZM7 5H4V8H20V5H17V6H15V5H9V6H7V5Z"></path></svg>

                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.job_posted') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium">{{ ($jobPost['job_posted_date']) ? date('d M, Y', strtotime($jobPost['job_posted_date'])) : '' }}</p>
                        </div>
                        <div class="mb-2 text-center">
                            <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="rgba(185,165,114,1)"><path d="M9 3V1H7V3H3C2.44772 3 2 3.44772 2 4V20C2 20.5523 2.44772 21 3 21H21C21.5523 21 22 20.5523 22 20V4C22 3.44772 21.5523 3 21 3H17V1H15V3H9ZM4 10H20V19H4V10ZM4 5H7V6H9V5H15V6H17V5H20V8H4V5ZM9.87862 10.9644L12 13.0858L14.1212 10.9644L15.5355 12.3785L13.4142 14.5001L15.5354 16.6212L14.1213 18.0354L12 15.9143L9.87855 18.0354L8.46442 16.6211L10.5857 14.5001L8.46436 12.3785L9.87862 10.9644Z"></path></svg>
                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.apply_deadline') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium">
                                {{ ($jobPost['deadline_date']) ? date('d M, Y', strtotime($jobPost['deadline_date'])) : '' }}
                            </p>
                        </div>
                        
                        <div class="mb-2 text-center">
                            <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="rgba(185,165,114,1)"><path d="M7 5V2C7 1.44772 7.44772 1 8 1H16C16.5523 1 17 1.44772 17 2V5H21C21.5523 5 22 5.44772 22 6V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V6C2 5.44772 2.44772 5 3 5H7ZM20 13H4V19H20V13ZM20 7H4V11H7V9H9V11H15V9H17V11H20V7ZM9 3V5H15V3H9Z"></path></svg>
                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.job_type') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium uppercase">{{ $jobPost['type'] }}</p>
                        </div>

                        <div class="mb-2 text-center">
                            <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="rgba(185,165,114,1)"><path d="M9 18H4V10H9V18ZM7 16V12H6V16H7ZM13 16V8H12V16H13ZM15 18H10V6H15V18ZM19 16V4H18V16H19ZM21 18H16V2H21V18ZM22 22H3V20H22V22Z"></path></svg>
                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.no_of_vacancies') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium uppercase">{{ $jobPost['no_of_vacancies'] }}</p>
                        </div>

                        <div class="mb-2 text-center">
                            <svg class="text-center m-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="36" height="36" fill="rgba(185,165,114,1)"><path d="M12 6.99999C16.4183 6.99999 20 10.5817 20 15C20 19.4183 16.4183 23 12 23C7.58172 23 4 19.4183 4 15C4 10.5817 7.58172 6.99999 12 6.99999ZM12 8.99999C8.68629 8.99999 6 11.6863 6 15C6 18.3137 8.68629 21 12 21C15.3137 21 18 18.3137 18 15C18 11.6863 15.3137 8.99999 12 8.99999ZM12 10.5L13.3225 13.1797L16.2798 13.6094L14.1399 15.6953L14.645 18.6406L12 17.25L9.35497 18.6406L9.86012 15.6953L7.72025 13.6094L10.6775 13.1797L12 10.5ZM18 1.99999V4.99999L16.6366 6.13755C15.5305 5.5577 14.3025 5.17884 13.0011 5.04948L13 1.99899L18 1.99999ZM11 1.99899L10.9997 5.04939C9.6984 5.17863 8.47046 5.55735 7.36441 6.13703L6 4.99999V1.99999L11 1.99899Z"></path></svg>
                            <p class="text-[#767F8C] my-1 uppercase">{{ __('frontend.years_of_experience') }}</p>
                            <p class="text-[#18191C] text-[15px] font-medium uppercase">{{ $jobPost['no_of_vacancies'] }}</p>
                        </div>
                    </div>
                </div>

                @if($jobPost['specialties'])
                    <div class="bg-white border border-gray-200 rounded-lg p-5">
                        <h3 class="text-lg font-medium mb-2">{{ __('frontend.specialities') }}</h3>
                        <div class="grid grid-cols-1 text-gray-700 text-sm ">
                            <ul class="list-disc list-inside">
                                @foreach ($jobPost['specialties'] as $spec)
                                    <li>{{ $spec ?? '-' }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection