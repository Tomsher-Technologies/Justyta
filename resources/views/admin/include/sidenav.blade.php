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

                @can('manage_staff')
                    <li class="has-child {{ areActiveRoutes(['staffs.create','staffs.index']) }}">
                        <a href="#" class="{{ areActiveRoutes(['staffs.create','staffs.index']) }}">
                            <span data-feather="users" class="nav-icon"></span>
                            <span class="menu-text">Membership Plans</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li>
                                <a class="{{ areActiveRoutes(['staffs.create']) }}" href="{{ route('staffs.create') }}">Add Plan</a>
                            </li>
                            <li>
                                <a class="{{ areActiveRoutes(['staffs.edit','staffs.index']) }}" href="{{ route('staffs.index') }}">All Plans</a>
                            </li>
                        </ul>
                    </li>
                @endcan

               
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
                                <li>
                                    <a class="{{ areActiveRoutes(['staffs.create']) }}" href="{{ route('staffs.create') }}">Add New Staff</a>
                                </li>
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
                                <li>
                                    <a class="{{ areActiveRoutes(['roles.create']) }}" href="{{ route('roles.create') }}">Add New Role</a>
                                </li>
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
