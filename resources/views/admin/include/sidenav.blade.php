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
                            class="{{ areActiveRoutes(['services.index', 'services.edit']) }}">
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
                    <li class="has-child {{ areActiveRoutes(['translators.create', 'translators.edit', 'translators.index']) }}">
                        <a href="#"
                            class="{{ areActiveRoutes(['translators.create', 'translators.edit', 'translators.index']) }}">
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
                                <a class="{{ areActiveRoutes(['translators.edit', 'translators.index']) }}"
                                    href="{{ route('translators.index') }}">All Translators</a>
                            </li>
                            @can('default_translator')
                                <li>
                                    <a class="{{ areActiveRoutes(['translators.default']) }}"
                                        href="{{ route('translators.default') }}">Set Default Translator</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @canany(['manage_plan', 'manage_dropdown_option'])
                    <li class="menu-title m-top-10">
                        <span>Settings</span> 
                    </li>
                    @can('manage_plan')
                        <li
                            class="has-child {{ areActiveRoutes(['membership-plans.create', 'membership-plans.edit', 'membership-plans.index']) }}">
                            <a href="#"
                                class="{{ areActiveRoutes(['membership-plans.create', 'membership-plans.edit', 'membership-plans.index']) }}">
                                {{-- <span data-feather="dollar-sign" class="nav-icon"></span> --}}
                                <i class="las la-dollar-sign nav-icon"></i>
                                {{-- <i class="fas fa-dollar-sign nav-icon"></i> --}}
                                <span class="menu-text">Membership Plans</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_plan')
                                    <li>
                                        <a class="{{ areActiveRoutes(['membership-plans.create']) }}"
                                            href="{{ route('membership-plans.create') }}">Add Plan</a>
                                    </li>
                                @endcan

                                <li>
                                    <a class="{{ areActiveRoutes(['membership-plans.edit', 'membership-plans.index']) }}"
                                        href="{{ route('membership-plans.index') }}">All Plans</a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @can('manage_dropdown_option')
                        <li class="">
                            <a href="{{ route('dropdowns.index') }}"
                                class="{{ areActiveRoutes(['dropdowns.index', 'dropdown-options.index']) }}">
                                <span data-feather="list" class="nav-icon"></span>
                                <span class="menu-text">Dropdowns</span>
                            </a>

                        </li>
                    @endcan



                    @can('manage_document_type')
                        <li class="">
                            <a href="{{ route('document-types.index') }}"
                                class="{{ areActiveRoutes(['document-types.index']) }}">
                                <span data-feather="file" class="nav-icon"></span>
                                <span class="menu-text">Document Types</span>
                            </a>
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
