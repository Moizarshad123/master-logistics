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
                <a href="javascript:;">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Trips</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.trips.index') }}">Active Trips</a>
                    </li>

                    <li>
                        <a href="{{ route('admin.closedTrips') }}">Closed Trips</a>
                    </li>
                    
                </ul>
            </li>

            <li>
                <a href="javascript:;">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Trailers</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.activeTrailersTrips') }}">Active Trailers Trips</a>
                    </li>

                    <li>
                        <a href="{{ route('admin.closeTrailersTrips') }}">Closed Trailers Trips</a>
                    </li>
                    
                </ul>
            </li>

            <li>
                <a  class="{{ request()->IS('admin/maintenances') ? 'active' : '' }}"  href="{{ route('admin.maintenances.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-bar-chart"></i>
                    </span>
                    <span>Maintenance (Workshop)</span>
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
                        <a href="{{ route('admin.vehicleSummaryReport') }}">Vehicle Summary Report</a>
                    </li>
                    
                    <li>
                        <a href="{{ route('admin.tripVehicleReport') }}">Trip Vehicle Report</a>
                    </li>

                    <li>
                        <a href="{{ route('admin.profitAndLossReport') }}">Profit and Loss Report</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.weeklyLabourReport') }}">Weekly Labour Report</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.balochLabourReport') }}">Baloch  Labour Report</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.disbursementSlip') }}">Disbursement Slip</a>
                    </li>

                    
                    
                </ul>
            </li>

            <li>
                <a href="javascript:;">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Customer Section</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.customer-heads.index')}}">Customer Heads</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customerHeadReport')}}">Customer Heads Report</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.customers.index')}}">Customers</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.amount-receivables.index')}}">Amount Receivables</a>
                    </li>

                    
                </ul>
            </li>

            <li>
                <a href="javascript:;">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Expenses Section</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.expense-categories.index') }}">Expense Category</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.expense-types.index') }}">Expense Types</a>
                    </li>
                    <li>
                        <a href="{{ route('admin.expense-from.index')}}">Expense From</a>
                    </li>
                </ul>
            </li>

            <li>
                <a href="javascript:;">
                    <span class="nav-link-icon">
                        <i class="bi bi-receipt"></i>
                    </span>
                    <span>Data Entry Section</span>
                </a>
                <ul>
                    <li>
                        <a href="{{ route('admin.sales.index') }}">Sell Sheet</a>
                        <a href="{{ route('admin.purchases.index') }}">Purchase Sheet</a>
                        <a href="{{ route('admin.destinations.index') }}">Destinations</a>
                        <a href="{{ route('admin.materials.index') }}">Materials</a>
                        <a href="{{ route('admin.drivers.index') }}">Drivers</a>
                        <a href="{{ route('admin.vehicles.index') }}">Vehicles</a>
                        <a href="{{ route('admin.wheelers.index') }}">Wheelers</a>
                    </li>
                    
                </ul>
            </li>

            <li>
                <a  href="{{route('admin.diesel.index') }}">
                    <span class="nav-link-icon">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <span>Fueling</span>
                </a>
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