@extends('layouts.web_translator', ['title' => 'Service Requests'])

@section('content')
    <div class="bg-white rounded-lg p-6">
        <h2 class="text-xl font-medium text-gray-900 mb-4">Translation</h2>

        <hr class="my-4 border-[#DFDFDF]" />

        <div class="grid grid-cols-1 md:grid-cols-12 items-end gap-4 mb-8">
            <div class="relative col-span-5">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                    </svg>
                </div>
                <input type="text" id="simple-search"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-3.5"
                    placeholder="Search here" required />
            </div>
            <div class="col-span-2">
                <form class="mx-auto">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">Date From and To</label>
                    <input type="date"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5" />
                </form>
            </div>
            <div class="col-span-2">
                <form class="mx-auto">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">Languages</label>
                    <select id="countries"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option selected>English - Arabic</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="FR">France</option>
                        <option value="DE">Germany</option>
                    </select>
                </form>
            </div>
            <div class="col-span-2">
                <form class="mx-auto">
                    <label for="countries" class="block mb-2 text-sm font-medium text-gray-900">Select Status</label>
                    <select id="countries"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3.5">
                        <option selected>Select lawyer</option>
                        <option value="US">United States</option>
                        <option value="CA">Canada</option>
                        <option value="FR">France</option>
                        <option value="DE">Germany</option>
                    </select>
                </form>
            </div>
            <div class="col-span-1">
                <a href="#"
                    class="bg-[#07683B] text-white h-[50px] w-full px-6 py-3.5 text-center rounded-lg t block">Filter</a>
            </div>
        </div>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <table class="w-full border">
                <thead class="text-md font-normal">
                    <tr class="bg-[#07683B] text-white font-normal">
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            Ref. No
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            Date and Time
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            Document Language
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            Translation Language
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            No.Of Pages
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-5 font-semibold text-start">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">REF-001234</td>
                        <td class="px-6 py-4">2025-05-21 10:30 AM</td>
                        <td class="px-6 py-4">English</td>
                        <td class="px-6 py-4">Arabic</td>
                        <td class="px-6 py-4">6</td>
                        <td class="px-6 py-4">Under Review</td>
                        <td class="px-6 py-4">
                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    <tr class="bg-[#EEF4F1] border-b text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">REF-001234</td>
                        <td class="px-6 py-4">2025-05-21 10:30 AM</td>
                        <td class="px-6 py-4">Arabic</td>
                        <td class="px-6 py-4">English</td>
                        <td class="px-6 py-4">4</td>
                        <td class="px-6 py-4">Progress</td>
                        <td class="px-6 py-4">
                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    <tr class="border-b text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">REF-001234</td>
                        <td class="px-6 py-4">2025-05-21 10:30 AM</td>
                        <td class="px-6 py-4">French</td>
                        <td class="px-6 py-4">Arabic</td>
                        <td class="px-6 py-4">8</td>
                        <td class="px-6 py-4">Rejected</td>
                        <td class="px-6 py-4">
                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                    <tr class="bg-[#EEF4F1] border-b text-[#4D4D4D]">
                        <td scope="row" class="px-6 py-4">REF-001234</td>
                        <td class="px-6 py-4">2025-05-21 10:30 AM</td>
                        <td class="px-6 py-4">English</td>
                        <td class="px-6 py-4">French</td>
                        <td class="px-6 py-4">15</td>
                        <td class="px-6 py-4">Under Review</td>
                        <td class="px-6 py-4">
                            <a href="#" class="flex items-center gap-0.5">
                                <svg class="w-6 h-6 text-[##4D4D4D]" aria-hidden="true"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                    viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z" />
                                    <path stroke="currentColor" stroke-width="1.7"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                <span>View</span>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
