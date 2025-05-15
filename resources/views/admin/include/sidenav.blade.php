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

                @can('manage_vendors')
                    <li class="has-child {{ areActiveRoutes(['vendors.create','vendors.index']) }}">
                        <a href="#" class="{{ areActiveRoutes(['vendors.create','vendors.index']) }}">
                            <span data-feather="users" class="nav-icon"></span>
                            <span class="menu-text">Law Firms</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li>
                                <a class="{{ areActiveRoutes(['vendors.create']) }}" href="{{ route('vendors.create') }}">Add New Law Firm</a>
                            </li>
                            <li>
                                <a class="{{ areActiveRoutes(['vendors.edit','vendors.index']) }}" href="{{ route('vendors.index') }}">All Law Firms</a>
                            </li>
                        </ul>
                    </li>
                @endcan


                @canany(['manage_plan'])
                    <li class="menu-title m-top-10">
                        <span>Settings</span>
                    </li>
                    @can('manage_plan')
                    <li class="has-child {{ areActiveRoutes(['membership-plans.create','membership-plans.edit','membership-plans.index']) }}">
                        <a href="#" class="{{ areActiveRoutes(['membership-plans.create','membership-plans.edit','membership-plans.index']) }}">
                            <span data-feather="dollar-sign" class="nav-icon"></span>
                            <span class="menu-text">Membership Plans</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            @can('add_plan')
                                <li>
                                    <a class="{{ areActiveRoutes(['membership-plans.create']) }}" href="{{ route('membership-plans.create') }}">Add Plan</a>
                                </li>
                            @endcan
                            
                            <li>
                                <a class="{{ areActiveRoutes(['membership-plans.edit','membership-plans.index']) }}" href="{{ route('membership-plans.index') }}">All Plans</a>
                            </li>
                        </ul>
                    </li>
                    @endcan
                @endcanany
               
                @canany(['manage_roles','manage_staff'])
                    <li class="menu-title m-top-10">
                        <span>Staff & Roles</span>
                    </li>

                    @can('manage_staff')
                        <li class="has-child {{ areActiveRoutes(['staffs.create','staffs.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['staffs.create','staffs.index']) }}">
                                <span data-feather="users" class="nav-icon"></span>
                                <span class="menu-text">Staffs</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_staff')
                                     <li>
                                        <a class="{{ areActiveRoutes(['staffs.create']) }}" href="{{ route('staffs.create') }}">Add New Staff</a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="{{ areActiveRoutes(['staffs.edit','staffs.index']) }}" href="{{ route('staffs.index') }}">All Staffs</a>
                                </li>
                            </ul>
                        </li>
                    @endcan

                    @can('manage_roles')
                        <li class="has-child {{ areActiveRoutes(['roles.create','roles.edit','roles.index']) }}">
                            <a href="#" class="{{ areActiveRoutes(['roles.create','roles.edit','roles.index']) }}">
                                <span data-feather="lock" class="nav-icon"></span>
                                <span class="menu-text">Roles & Permissions</span>
                                <span class="toggle-icon"></span>
                            </a>
                            <ul>
                                @can('add_role')
                                    <li>
                                        <a class="{{ areActiveRoutes(['roles.create']) }}" href="{{ route('roles.create') }}">Add New Role</a>
                                    </li>
                                @endcan
                                <li>
                                    <a class="{{ areActiveRoutes(['roles.edit','roles.index']) }}" href="{{ route('roles.index') }}">All Roles</a>
                                </li>
                            </ul>
                        </li>
                    @endcan
                @endcanany
               
            </ul>
        </div>
    </div>
</aside>
