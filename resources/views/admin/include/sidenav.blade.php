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
                    <li class="has-child {{ areActiveRoutes(['vendors.create', 'vendors.index']) }}">
                        <a href="#" class="{{ areActiveRoutes(['vendors.create', 'vendors.index']) }}">
                            <span data-feather="users" class="nav-icon"></span>
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


                @canany(['manage_plan', 'manage_dropdown_option'])
                    <li class="menu-title m-top-10">
                        <span>Settings</span>
                    </li>
                    @can('manage_plan')
                        <li
                            class="has-child {{ areActiveRoutes(['membership-plans.create', 'membership-plans.edit', 'membership-plans.index']) }}">
                            <a href="#"
                                class="{{ areActiveRoutes(['membership-plans.create', 'membership-plans.edit', 'membership-plans.index']) }}">
                                <span data-feather="dollar-sign" class="nav-icon"></span>
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

                @canany(['manage_website_settings'])
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
                @endcanany

                @canany(['manage_roles', 'manage_staff'])
                    <li class="menu-title m-top-10">
                        <span>Staff & Roles</span>
                    </li>

                    @can('manage_staff')
                        <li class="has-child {{ areActiveRoutes(['staffs.create', 'staffs.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['staffs.create', 'staffs.index']) }}">
                                <span data-feather="users" class="nav-icon"></span>
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
                                        <a class="{{ areActiveRoutes(['roles.create']) }}" href="{{ route('roles.create') }}">Add
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
