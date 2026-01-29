<!--**********************************
    Sidebar start
***********************************-->
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-grid"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('orders.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('orders.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}">
                    <i class="bi bi-file-text"></i>
                    <span class="nav-text">Orders</span>
                </a>
                <ul class="{{ request()->routeIs('orders.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.index') || request()->routeIs('orders.show') || request()->routeIs('orders.edit') ? 'mm-active' : '' }}">Orders</a></li>
                    <li><a href="{{ route('orders.calendar') }}" class="{{ request()->routeIs('orders.calendar') ? 'mm-active' : '' }}">Order Calendar</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('customers.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('customers.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('customers.*') ? 'true' : 'false' }}">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Customers</span>
                </a>
                <ul class="{{ request()->routeIs('customers.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('customers.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.index') || request()->routeIs('customers.show') || request()->routeIs('customers.edit') ? 'mm-active' : '' }}">Customer List</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('payments.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('payments.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('payments.*') ? 'true' : 'false' }}">
                    <i class="bi bi-credit-card"></i>
                    <span class="nav-text">Payments</span>
                </a>
                <ul class="{{ request()->routeIs('payments.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('payments.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.index') || request()->routeIs('payments.show') || request()->routeIs('payments.edit') ? 'mm-active' : '' }}">Payments List</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('inventory.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('inventory.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('inventory.*') ? 'true' : 'false' }}">
                    <i class="bi bi-box-seam"></i>
                    <span class="nav-text">Inventory</span>
                </a>
                <ul class="{{ request()->routeIs('inventory.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('inventory.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.index') || request()->routeIs('inventory.show') || request()->routeIs('inventory.edit') ? 'mm-active' : '' }}">Item List</a></li>
                    <li><a href="{{ route('inventory.stock-in') }}" class="{{ request()->routeIs('inventory.stock-in*') ? 'mm-active' : '' }}">Stock In</a></li>
                    <li><a href="{{ route('inventory.stock-out') }}" class="{{ request()->routeIs('inventory.stock-out*') ? 'mm-active' : '' }}">Stock Out</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('vendors.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('vendors.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('vendors.*') ? 'true' : 'false' }}">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Vendors</span>
                </a>
                <ul class="{{ request()->routeIs('vendors.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('vendors.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.index') || request()->routeIs('vendors.show') || request()->routeIs('vendors.edit') ? 'mm-active' : '' }}">Vendor List</a></li>
                    <li><a href="{{ route('vendors.create') }}" class="{{ request()->routeIs('vendors.create') ? 'mm-active' : '' }}">Add Vendor</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('equipment.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('equipment.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('equipment.*') ? 'true' : 'false' }}">
<i class="bi bi-tools"></i>
                    <span class="nav-text">Equipment</span>
                </a>
                <ul class="{{ request()->routeIs('equipment.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('equipment.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('equipment.index') }}" class="{{ request()->routeIs('equipment.index') || request()->routeIs('equipment.show') || request()->routeIs('equipment.edit') ? 'mm-active' : '' }}">Equipment List</a></li>
                </ul>
            </li>
            @hasPermission('staff.view')
            <li class="{{ request()->routeIs('staff.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('staff.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('staff.*') ? 'true' : 'false' }}">
                    <i class="bi bi-people-fill"></i>
                    <span class="nav-text">Staff</span>
                </a>
                <ul class="{{ request()->routeIs('staff.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('staff.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('staff.index') }}" class="{{ request()->routeIs('staff.index') || request()->routeIs('staff.show') || request()->routeIs('staff.edit') || request()->routeIs('staff.workload') || request()->routeIs('staff.performance') ? 'mm-active' : '' }}">Staff List</a></li>
                </ul>
            </li>
            @endhasPermission
            @hasPermission('attendance.view')
            <li class="{{ request()->routeIs('attendance.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('attendance.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('attendance.*') ? 'true' : 'false' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span class="nav-text">Attendance</span>
                </a>
                <ul class="{{ request()->routeIs('attendance.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('attendance.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('attendance.index') }}" class="{{ request()->routeIs('attendance.index') ? 'mm-active' : '' }}">Attendance List</a></li>
                    @hasPermission('attendance.create')
                    <li><a href="{{ route('attendance.create') }}" class="{{ request()->routeIs('attendance.create') ? 'mm-active' : '' }}">Mark Attendance</a></li>
                    <li><a href="{{ route('attendance.bulk') }}" class="{{ request()->routeIs('attendance.bulk*') ? 'mm-active' : '' }}">Bulk Mark</a></li>
                    @endhasPermission
                    <li><a href="{{ route('attendance.report') }}" class="{{ request()->routeIs('attendance.report') ? 'mm-active' : '' }}">Attendance Report</a></li>
                </ul>
            </li>
            @endhasPermission
            <li class="{{ request()->routeIs('reports.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('reports.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                    <i class="bi bi-graph-up"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <ul class="{{ request()->routeIs('reports.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('reports.orders') }}" class="{{ request()->routeIs('reports.orders') ? 'mm-active' : '' }}">Orders</a></li>
                    <li><a href="{{ route('reports.payments') }}" class="{{ request()->routeIs('reports.payments') ? 'mm-active' : '' }}">Payments</a></li>
                    <li><a href="{{ route('reports.expenses') }}" class="{{ request()->routeIs('reports.expenses') ? 'mm-active' : '' }}">Expenses</a></li>
                    <li><a href="{{ route('reports.customers') }}" class="{{ request()->routeIs('reports.customers') ? 'mm-active' : '' }}">Customers</a></li>
                    <li><a href="{{ route('reports.profit-loss') }}" class="{{ request()->routeIs('reports.profit-loss') ? 'mm-active' : '' }}">Profit & Loss</a></li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'mm-active' : '' }}"><a class="has-arrow {{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'true' : 'false' }}">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Settings</span>
                </a>
                <ul class="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'mm-show' : '' }}" aria-expanded="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'true' : 'false' }}">
                    @hasPermission('users.view')
                    <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'mm-active' : '' }}">User Management</a></li>
                    @endhasPermission
                    <li><a href="{{ route('settings.order-statuses') }}" class="{{ request()->routeIs('settings.order-statuses*') ? 'mm-active' : '' }}">Order Statuses</a></li>
                    <li><a href="{{ route('settings.event-times') }}" class="{{ request()->routeIs('settings.event-times*') ? 'mm-active' : '' }}">Order Event Times</a></li>
                    <li><a href="{{ route('settings.order-types') }}" class="{{ request()->routeIs('settings.order-types*') ? 'mm-active' : '' }}">Order Types</a></li>
                    <li><a href="{{ route('settings.inventory-units') }}" class="{{ request()->routeIs('settings.inventory-units*') ? 'mm-active' : '' }}">Inventory Units</a></li>
                    <li><a href="{{ route('settings.equipment-categories') }}" class="{{ request()->routeIs('settings.equipment-categories*') ? 'mm-active' : '' }}">Equipment Categories</a></li>
                    <li><a href="{{ route('settings.staff-roles') }}" class="{{ request()->routeIs('settings.staff-roles*') ? 'mm-active' : '' }}">Staff Roles</a></li>
                </ul>
            </li>
        </ul>
        <div class="copyright">
            <p><strong>Catering Management System</strong> © {{ date('Y') }} All Rights Reserved</p>
        </div>
    </div>
</div>
<!--**********************************
    Sidebar end
***********************************-->