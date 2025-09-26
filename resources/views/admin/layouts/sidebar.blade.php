<!-- menu -->

<div class="menu">
    <div class="menu-header">
        <a href="{{ route('admin.dashboard') }}" class="menu-header-logo">
            {{-- <img src="{{ asset('admin/logo.jpg')}}" alt="logo" style="width: 200px"> --}}
        </a>
        <a href="{{ url('/')}}" class="btn btn-sm menu-close-btn">
            <i class="bi bi-x"></i>
        </a>
    </div>
    <div class="menu-body">
        <div class="dropdown">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center" data-bs-toggle="dropdown">
                <div class="avatar me-3">
                    <img src="{{ asset('admin/assets/images/user/man_avatar3.jpg')}}"
                         class="rounded-circle" alt="image">
                </div>
                <div>
                    <div class="fw-bold">Master Logistics</div>
                </div>
            </a>
            
        </div>
        <ul>

            <li>
                <a  class="{{ request()->IS('admin/dashboard') ? 'active' : '' }}"  href="{{ route('admin.dashboard') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a  class="{{ request()->IS('admin/expense-types') ? 'active' : '' }}"  href="{{ route('admin.expense-types.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Expense Types</span>
                </a>
            </li>

            <li>
                <a  class="{{ request()->IS('admin/drivers') ? 'active' : '' }}"  href="{{ route('admin.drivers.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Drivers</span>
                </a>
            </li>

            <li>
                <a  class="{{ request()->IS('admin/vehicles') ? 'active' : '' }}"  href="{{ route('admin.vehicles.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Vehicles</span>
                </a>
            </li>

            <li>
                <a  class="{{ request()->IS('admin/wheelers') ? 'active' : '' }}"  href="{{ route('admin.wheelers.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Wheelers</span>
                </a>
            </li>


            <li>
                <a  class="{{ request()->IS('admin/trips') ? 'active' : '' }}"  href="{{ route('admin.trips.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Trips</span>
                </a>
            </li>


            <li>
                <a href="javascript:;">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Reports</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.tripVehicleReport') }}">Trip Vehicle Report</a>
                    </li>
                    
                </ul>
            </li>

            <li>
                <a  href="{{route('admin.logout') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>