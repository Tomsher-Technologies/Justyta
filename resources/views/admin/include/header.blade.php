<header class="header-top">
    <nav class="navbar navbar-light">
        <div class="navbar-left">
            <a href="" class="sidebar-toggle">
                <img class="svg" src="{{ asset('assets/img/svg/bars.svg') }}" alt="img"></a>
            <a class="navbar-brand" href="#"><img class="dark" src="{{ asset('assets/img/logo-text.svg') }}" alt="svg"><img
                    class="light" src="{{ asset('assets/img/logo-text.svg') }}" alt="img"></a>
            
            <div class="top-menu">

            </div>
        </div>
        <!-- ends: navbar-left -->

        <div class="navbar-right">
            <ul class="navbar-right__menu">
                <li class="nav-search d-none">
                    <a href="#" class="search-toggle">
                        <i class="la la-search"></i>
                        <i class="la la-times"></i>
                    </a>
                    <form action="/" class="search-form-topMenu" autocomplete="off">
                        <span class="search-icon" data-feather="search"></span>
                        <input class="form-control mr-sm-2 box-shadow-none" type="text" placeholder="Search...">
                    </form>
                </li>
              
                @php
                    $unreadNotifications = getUnreadNotifications();
                @endphp
                <!-- ends: nav-message -->
                <li class="nav-notification">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle {{ $unreadNotifications->count() ? 'has-unread' : '' }}">
                            <span data-feather="bell"></span></a>
                        <div class="dropdown-wrapper">
                            <h2 class="dropdown-wrapper__title">Notifications <span
                                    class="badge-circle badge-warning ml-1">{{ $unreadNotifications->count() }}</span></h2>
                            <ul>
                                @forelse($unreadNotifications as $notification)
                                    <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                        <div class="nav-notification__type nav-notification__type--primary">
                                            <span data-feather="inbox"></span>
                                        </div>
                                        <div class="nav-notification__details">
                                            <p>
                                                {{ $notification->data['message'] ?? 'New Notification' }}
                                            </p>
                                            <p>
                                                <span class="time-posted">{{ $notification->created_at->format('d,M Y h:i A') }}</span>
                                            </p>
                                        </div>
                                    </li>
                                @empty
                                    <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                        <div class="nav-notification__details">
                                            <p>No new notifications</p>
                                        </div>
                                    </li>
                                @endforelse

                                
                               
                            </ul>
                            <a href="{{ route('notifications.index') }}" class="dropdown-wrapper__more">See all incoming activity</a>
                        </div>
                    </div>
                </li>
               
                <!-- ends: .nav-flag-select -->
                <li class="nav-author">
                    <div class="dropdown-custom">
                        <a href="javascript:;" class="nav-item-toggle"><img src="{{ asset('assets/img/author-nav.jpg') }}" alt=""
                                class="rounded-circle"></a>
                        <div class="dropdown-wrapper">
                            <div class="nav-author__info">
                                <div class="author-img">
                                    <img src="{{ asset('assets/img/author-nav.jpg') }}" alt="" class="rounded-circle">
                                </div>
                                <div>
                                    <h6>{{ auth()->user()->name ?? '' }}</h6>
                                    {{-- <span>UI Designer</span> --}}
                                </div>
                            </div>
                            <div class="nav-author__options">
                                <ul>
                                    <li>
                                        <a href="">
                                            <span data-feather="user"></span> Profile</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.logout') }}">
                                            <span data-feather="log-out"></span> Sign Out</a>
                                    </li>
                                </ul>
                                {{-- <a href="" class="nav-author__signout">
                                    <span data-feather="log-out"></span> </a> --}}
                            </div>
                        </div>
                        <!-- ends: .dropdown-wrapper -->
                    </div>
                </li>
                <!-- ends: .nav-author -->
            </ul>
            <!-- ends: .navbar-right__menu -->
            <div class="navbar-right__mobileAction d-md-none">
                <a href="#" class="btn-search">
                    <span data-feather="search"></span>
                    <span data-feather="x"></span></a>
                <a href="#" class="btn-author-action">
                    <span data-feather="more-vertical"></span></a>
            </div>
        </div>
        <!-- ends: .navbar-right -->
    </nav>
</header>
