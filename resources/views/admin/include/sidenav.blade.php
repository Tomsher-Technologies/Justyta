<aside class="sidebar-wrapper">
    <div class="sidebar sidebar-collapse" id="sidebar">
        <div class="sidebar__menu-group">
            <ul class="sidebar_nav">
                <li class="menu-title">
                    <span>Main menu</span>
                </li>
                <li class="">
                    <a href="{{ route('admin.dashboard') }}" class="{{ areActiveRoutes(['admin.dashboard']) }}">
                        <span data-feather="home" class="nav-icon"></span>
                        <span class="menu-text">Dashboard</span>
                    </a>

                </li>

                @can('manage_service')
                    <li class="">
                        <a href="{{ route('services.index') }}"
                            class="{{ areActiveRoutes(['services.index', 'services.edit','expert-pricing.index','expert-pricing.create','expert-pricing.edit','request-pricing.index','request-pricing.create','request-pricing.edit']) }}">
                            <span data-feather="layers" class="nav-icon"></span>
                            <span class="menu-text">Services</span>
                        </a>
                    </li>
                @endcan

                @can('manage_vendors')
                    <li class="has-child {{ areActiveRoutes(['vendors.create', 'vendors.edit', 'vendors.index']) }}">
                        <a href="#"
                            class="{{ areActiveRoutes(['vendors.create', 'vendors.edit', 'vendors.index']) }}">
                            {{-- <span data-feather="user-plus" class="nav-icon"></span> --}}
                            <i class="la la-building nav-icon"></i>
                            <span class="menu-text">Law Firms</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            @can('add_vendor')
                                <li>
                                    <a class="{{ areActiveRoutes(['vendors.create']) }}"
                                        href="{{ route('vendors.create') }}">Add New Law Firm</a>
                                </li>
                            @endcan

                            <li>
                                <a class="{{ areActiveRoutes(['vendors.edit', 'vendors.index']) }}"
                                    href="{{ route('vendors.index') }}">All Law Firms</a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('manage_lawyers')
                    <li class="has-child {{ areActiveRoutes(['lawyers.create', 'lawyers.edit', 'lawyers.index']) }}">
                        <a href="#"
                            class="{{ areActiveRoutes(['lawyers.create', 'lawyers.edit', 'lawyers.index']) }}">
                            {{-- <span data-feather="users" class="nav-icon"></span> --}}
                            <i class="la la-users nav-icon"></i>
                            <span class="menu-text">Lawyers</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            @can('add_lawyer')
                                <li>
                                    <a class="{{ areActiveRoutes(['lawyers.create']) }}"
                                        href="{{ route('lawyers.create') }}">Add New Lawyer</a>
                                </li>
                            @endcan

                            <li>
                                <a class="{{ areActiveRoutes(['lawyers.edit', 'lawyers.index']) }}"
                                    href="{{ route('lawyers.index') }}">All Lawyers</a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('manage_job_post')
                    <li class="has-child {{ areActiveRoutes(['job-posts.create', 'job-posts.edit', 'job-posts.index']) }}">
                        <a href="#"
                            class="{{ areActiveRoutes(['job-posts.create', 'job-posts.edit', 'job-posts.index']) }}">
                            <span data-feather="briefcase" class="nav-icon"></span>
                            <span class="menu-text">Job Posts</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            @can('add_job_post')
                                <li>
                                    <a class="{{ areActiveRoutes(['job-posts.create']) }}"
                                        href="{{ route('job-posts.create') }}">Add New Job Post</a>
                                </li>
                            @endcan

                            <li>
                                <a class="{{ areActiveRoutes(['job-posts.edit', 'job-posts.index']) }}"
                                    href="{{ route('job-posts.index') }}">All Job Posts</a>
                            </li>
                        </ul>
                    </li>
                @endcan

                @can('manage_translators')
                    <li class="has-child {{ areActiveRoutes(['translators.create', 'translators.edit', 'translators.index','default-translators.history','translators.default','translator-pricing.create','translator-pricing.edit','translator-pricing']) }}">
                        <a href="#" class="{{ areActiveRoutes(['translators.create', 'translators.edit', 'translators.index','default-translators.history','translators.default','translator-pricing.create','translator-pricing.edit','translator-pricing']) }}">
                            {{-- <span data-feather="users" class="nav-icon"></span> --}}
                            <i class="las la-language nav-icon"></i>
                            <span class="menu-text">Translators</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            @can('add_translator')
                                <li>
                                    <a class="{{ areActiveRoutes(['translators.create']) }}"
                                        href="{{ route('translators.create') }}">Add New Translators</a>
                                </li>
                            @endcan

                            <li>
                                <a class="{{ areActiveRoutes(['translators.edit', 'translators.index','translator-pricing.create','translator-pricing.edit','translator-pricing']) }}"
                                    href="{{ route('translators.index') }}">All Translators</a>
                            </li>
                            @can('default_translator')
                                <li>
                                    <a class="{{ areActiveRoutes(['translators.default','default-translators.history']) }}"
                                        href="{{ route('translators.default') }}">Set Default Translator</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('manage_service_requests')
                    <li class="menu-title m-top-10">
                        <span>Reports</span> 
                    </li>

                    @can('manage_service_requests')
                        <li class="">
                            <a href="{{ route('service-requests.index') }}"
                                class="{{ areActiveRoutes(['service-requests.index','service-request-details']) }}">
                                <span data-feather="list" class="nav-icon"></span>
                                <span class="menu-text">Service Requests</span>
                            </a>
                        </li>
                    @endcan

                    @can('manage_translation_requests')
                        <li class="">
                            <a href="{{ route('legal-translation-requests.index') }}"
                                class="{{ areActiveRoutes(['legal-translation-requests.index','translation-request-details']) }}">
                                <span class="las la-language nav-icon"></span>
                                <span class="menu-text">Legal Translation Requests</span>
                            </a>
                        </li>
                    @endcan

                    @can('manage_training_requests')
                        <li class="">
                            <a href="{{ route('training-requests.index') }}"
                                class="{{ areActiveRoutes(['training-requests.index']) }}">
                                <span data-feather="file-text" class="nav-icon"></span>
                                <span class="menu-text">Training Requests</span>
                            </a>
                        </li>
                    @endcan
                @endcan

                @can('manage_user_feedbacks')
                    <li class="menu-title m-top-10">
                        <span>User Feedbacks</span> 
                    </li>
                    @can('user_contacts')
                        <li class="">
                            <a href="{{ route('user-contacts.feedback') }}"
                                class="{{ areActiveRoutes(['user-contacts.feedback']) }}">
                                <span data-feather="mail" class="nav-icon"></span>
                                <span class="menu-text">Contacts</span>
                            </a>
                        </li>
                    @endcan
                    @can('user_ratings')
                        <li class="">
                            <a href="{{ route('user-ratings.feedback') }}"
                                class="{{ areActiveRoutes(['user-ratings.feedback']) }}">
                                <span data-feather="star" class="nav-icon"></span>
                                <span class="menu-text">User Ratings</span>
                            </a>
                        </li>
                    @endcan
                    @can('reported_problems')
                        <li class="">
                            <a href="{{ route('user-reported-problems.feedback') }}"
                                class="{{ areActiveRoutes(['user-reported-problems.feedback']) }}">
                                <span data-feather="alert-triangle" class="nav-icon"></span>
                                <span class="menu-text">Reported Problems</span>
                            </a>
                        </li>
                    @endcan
                @endcan
               

                @canany(['manage_plan', 'manage_dropdown_option','manage_ads'])
                    <li class="menu-title m-top-10">
                        <span>Settings</span> 
                    </li>

                    @can('manage_ads')
                        <li
                            class="has-child {{ areActiveRoutes(['ads.create', 'ads.edit', 'ads.index']) }}">
                            <a href="#"
                                class="{{ areActiveRoutes(['ads.create', 'ads.edit', 'ads.index']) }}">
                              
                                <span data-feather="tv" class="nav-icon"></span>
                                <span class="menu-text">Ads</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_ads')
                                    <li>
                                        <a class="{{ areActiveRoutes(['ads.create']) }}"
                                            href="{{ route('ads.create') }}">Add Ad</a>
                                    </li>
                                @endcan

                                <li>
                                    <a class="{{ areActiveRoutes(['ads.edit', 'ads.index']) }}"
                                        href="{{ route('ads.index') }}">All Ads</a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @can('manage_plan')
                        <li class="">
                            <a href="{{ route('membership-plans.index') }}"
                                class="{{ areActiveRoutes(['membership-plans.edit', 'membership-plans.index','plan-pricing.create','plan-pricing.edit','plan-pricing']) }}">
                                <i class="las la-dollar-sign nav-icon"></i>
                                <span class="menu-text">Membership Plans</span>
                            </a>
                        </li>
                    @endcan

                    @can('manage_dropdown_option')
                        <li class="has-child {{ areActiveRoutes(['dropdowns.index', 'dropdown-options.index', 'document-types.index','free-zones.index','contract-types.index','court-requests.index','public-prosecutions.index','license-types.index','countries.index','emirates.index','case-types.index','request-types.index','request-titles.index']) }}">
                            <a href="#"
                                class="{{ areActiveRoutes(['dropdowns.index', 'dropdown-options.index','document-types.index','free-zones.index','contract-types.index','court-requests.index','public-prosecutions.index','license-types.index','countries.index','emirates.index','case-types.index','request-types.index','request-titles.index']) }}">
                                <span data-feather="list" class="nav-icon"></span>
                                <span class="menu-text">Dropdown Contents</span>
                                <span class="toggle-icon"></span>
                            </a>

                            <ul>
                                <li>
                                    <a class="{{ areActiveRoutes(['emirates.index']) }}"
                                        href="{{ route('emirates.index') }}">Emirates</a>
                                </li>

                                <li>
                                    <a class="{{ areActiveRoutes(['case-types.index']) }}"
                                        href="{{ route('case-types.index') }}">Case Types</a>
                                </li>

                                <li>
                                    <a class="{{ areActiveRoutes(['contract-types.index']) }}"
                                        href="{{ route('contract-types.index') }}">Contract Types</a>
                                </li>

                                <li>
                                    <a class="{{ areActiveRoutes(['countries.index']) }}"
                                        href="{{ route('countries.index') }}">Countries</a>
                                </li>

                                {{-- <li>
                                    <a class="{{ areActiveRoutes(['court-requests.index']) }}"
                                        href="{{ route('court-requests.index') }}">Court Requests</a>
                                </li> --}}

                                <li>
                                    <a class="{{ areActiveRoutes(['document-types.index']) }}"
                                        href="{{ route('document-types.index') }}">Document Types</a>
                                </li>

                                <li>
                                    <a class="{{ areActiveRoutes(['free-zones.index']) }}"
                                        href="{{ route('free-zones.index') }}">Free Zones</a>
                                </li>

                                <li>
                                    <a class="{{ areActiveRoutes(['license-types.index']) }}"
                                        href="{{ route('license-types.index') }}">License Types & Activities</a>
                                </li>

                                {{-- <li>
                                    <a class="{{ areActiveRoutes(['public-prosecutions.index']) }}"
                                        href="{{ route('public-prosecutions.index') }}">Public Prosecution Types</a>
                                </li> --}}

                                <li>
                                    <a class="{{ areActiveRoutes(['request-types.index','request-titles.index']) }}"
                                        href="{{ route('request-types.index') }}">Request Types & Titles</a>
                                </li>

                                <li>
                                    <a class="{{ areActiveRoutes(['dropdowns.index', 'dropdown-options.index']) }}"
                                        href="{{ route('dropdowns.index') }}">Other Dropdowns</a>
                                </li>

                            </ul>
                        </li>
                    @endcan
                @endcanany

                @canany(['manage_website_settings', 'manage_news'])
                    <li class="menu-title m-top-10">
                        <span>Wbsite Contents</span>
                    </li>

                    @can('update_header')
                        <li class="">
                            <a href="{{ route('dropdowns.index') }}"
                                class="{{ areActiveRoutes(['dropdowns.index', 'dropdown-options.index']) }}">
                                <span data-feather="list" class="nav-icon"></span>
                                <span class="menu-text">Header</span>
                            </a>

                        </li>
                    @endcan

                    @can('update_footer')
                        <li class="">
                            <a href="{{ route('dropdowns.index') }}"
                                class="{{ areActiveRoutes(['dropdowns.index', 'dropdown-options.index']) }}">
                                <span data-feather="list" class="nav-icon"></span>
                                <span class="menu-text">Footer</span>
                            </a>

                        </li>
                    @endcan

                    @can('update_page_contents')
                        <li class="">
                            <a href="{{ route('pages.index') }}" class="{{ areActiveRoutes(['pages.index', 'pages.edit']) }}">
                                <span data-feather="file-text" class="nav-icon"></span>
                                <span class="menu-text">Page Contents</span>
                            </a>

                        </li>
                    @endcan

                    @can('manage_news')
                        <li class="has-child {{ areActiveRoutes(['news.create', 'news.edit', 'news.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['news.create', 'news.edit', 'news.index']) }}">
                                <span data-feather="globe" class="nav-icon"></span>
                                <span class="menu-text">News</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_news')
                                    <li>
                                        <a class="{{ areActiveRoutes(['news.create']) }}" href="{{ route('news.create') }}">Add
                                            New News</a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="{{ areActiveRoutes(['news.edit', 'news.index']) }}"
                                        href="{{ route('news.index') }}">All News</a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @can('manage_faqs')
                        <li class="has-child {{ areActiveRoutes(['faqs.create', 'faqs.edit', 'faqs.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['faqs.create', 'faqs.edit', 'faqs.index']) }}">
                                <span data-feather="help-circle" class="nav-icon"></span>
                                <span class="menu-text">FAQs</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_faq')
                                    <li>
                                        <a class="{{ areActiveRoutes(['faqs.create']) }}" href="{{ route('faqs.create') }}">Add
                                            New FAQ</a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="{{ areActiveRoutes(['faqs.edit', 'faqs.index']) }}"
                                        href="{{ route('faqs.index') }}">All FAQs</a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                @endcanany

                @canany(['manage_roles', 'manage_staff'])
                    <li class="menu-title m-top-10">
                        <span>Staff & Roles</span>
                    </li>

                    @can('manage_staff')
                        <li class="has-child {{ areActiveRoutes(['staffs.create', 'staffs.edit', 'staffs.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['staffs.create', 'staffs.edit', 'staffs.index']) }}">
                                {{-- <span data-feather="users" class="nav-icon"></span> --}}
                                <i class="la la-user-lock nav-icon"></i>
                                <span class="menu-text">Staffs</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_staff')
                                    <li>
                                        <a class="{{ areActiveRoutes(['staffs.create']) }}"
                                            href="{{ route('staffs.create') }}">Add New Staff</a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="{{ areActiveRoutes(['staffs.edit', 'staffs.index']) }}"
                                        href="{{ route('staffs.index') }}">All Staffs</a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @can('manage_roles')
                        <li class="has-child {{ areActiveRoutes(['roles.create', 'roles.edit', 'roles.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['roles.create', 'roles.edit', 'roles.index']) }}">
                                <span data-feather="lock" class="nav-icon"></span>
                                <span class="menu-text">Roles & Permissions</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_role')
                                    <li>
                                        <a class="{{ areActiveRoutes(['roles.create']) }}"
                                            href="{{ route('roles.create') }}">Add
                                            New Role</a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="{{ areActiveRoutes(['roles.edit', 'roles.index']) }}"
                                        href="{{ route('roles.index') }}">All Roles</a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                @endcanany

            </ul>
        </div>
    </div>
</aside>
