 <section class="bg-[#FFF7F0] px-10 md:px-10 lg:px-10 py-10 mt-8">
        <h4 class="mb-3 text-[30px] text-[#034833] font-cinzel">{!! $section->getTranslation('title', $lang) !!}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
            
            @php
                $serviceIds = json_decode($section->services);
                $services = getServiceData($serviceIds);
            @endphp

            @foreach ($services as $service)
                <div class="bg-white p-8 border !border-[#F5E4BA]">
                    <img src="{{ asset(getUploadedImage($service->icon)) }}" alt="Service Image" class="mb-5 w-16 h-16 object-contain">
                    <h3 class="mb-6 min-h-min md:min-h-[46px]">{{ $service->getTranslation('title', $lang) }}</h3>

                    @if($service->slug === 'online-live-consultancy')
                        <a href="{{ route('service.online.consultation') }}" class="flex items-center gap-4">
                    @else
                        <a href="{{ route('service.request.form',['slug' => $service->slug]) }}" class="flex items-center gap-4">
                    @endif
                        <span>{{ __('frontend.go_to_service') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="23" viewBox="0 0 22 23"
                        fill="none">
                            <path d="M8.2137 6.52631L15.8141 6.34811C16.0203 6.34808 16.197 6.42169 16.3443 6.56895C16.4916 6.71622 16.5652 6.89296 16.5651 7.09917L16.3869 14.6995C16.3279 15.1709 16.0775 15.4213 15.6356 15.4509C15.1643 15.3921 14.9139 15.1417 14.8846 14.6998L15.0182 8.95536L7.50481 16.4688C7.15125 16.7634 6.79775 16.7635 6.4443 16.469C6.14978 16.1155 6.14984 15.762 6.4445 15.4084L13.9579 7.89505L8.21342 8.0287C7.77154 7.99932 7.52119 7.74897 7.46236 7.27765C7.49191 6.83576 7.74235 6.58531 8.2137 6.52631Z"
                                fill="#07683B" />
                        </svg>
                    </a>
                </div>
            @endforeach
            
        </div>
    </section>