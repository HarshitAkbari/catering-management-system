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
            <li><a class="has-arrow {{ request()->routeIs('orders.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}">
                    <i class="bi bi-file-text"></i>
                    <span class="nav-text">Orders</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.index') ? 'mm-active' : '' }}">Orders</a></li>
                    <li><a href="{{ route('orders.create') }}" class="{{ request()->routeIs('orders.create') ? 'mm-active' : '' }}">Create Order</a></li>
                    <li><a href="{{ route('orders.calendar') }}" class="{{ request()->routeIs('orders.calendar') ? 'mm-active' : '' }}">Order Calendar</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('customers.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('customers.*') ? 'true' : 'false' }}">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Customers</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('customers.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.index') ? 'mm-active' : '' }}">Customer List</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('payments.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('payments.*') ? 'true' : 'false' }}">
                    <i class="bi bi-credit-card"></i>
                    <span class="nav-text">Payments</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('payments.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.index') ? 'mm-active' : '' }}">Payments List</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('inventory.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('inventory.*') ? 'true' : 'false' }}">
                    <i class="bi bi-box-seam"></i>
                    <span class="nav-text">Inventory</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('inventory.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.index') ? 'mm-active' : '' }}">Item List</a></li>
                    <li><a href="{{ route('inventory.create') }}" class="{{ request()->routeIs('inventory.create') ? 'mm-active' : '' }}">Add Inventory Item</a></li>
                    <li><a href="{{ route('inventory.stock-in') }}" class="{{ request()->routeIs('inventory.stock-in*') ? 'mm-active' : '' }}">Stock In</a></li>
                    <li><a href="{{ route('inventory.stock-out') }}" class="{{ request()->routeIs('inventory.stock-out*') ? 'mm-active' : '' }}">Stock Out</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('vendors.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('vendors.*') ? 'true' : 'false' }}">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Vendors</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('vendors.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.index') ? 'mm-active' : '' }}">Vendor List</a></li>
                    <li><a href="{{ route('vendors.create') }}" class="{{ request()->routeIs('vendors.create') ? 'mm-active' : '' }}">Add Vendor</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('equipment.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('equipment.*') ? 'true' : 'false' }}">
                    <i class="bi bi-tools"></i>
                    <span class="nav-text">Equipment</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('equipment.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('equipment.index') }}" class="{{ request()->routeIs('equipment.index') ? 'mm-active' : '' }}">Equipment List</a></li>
                    <li><a href="{{ route('equipment.create') }}" class="{{ request()->routeIs('equipment.create') ? 'mm-active' : '' }}">Add Equipment</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('reports.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                    <i class="bi bi-graph-up"></i>
                    <span class="nav-text">Reports</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                    <li><a href="{{ route('reports.orders') }}" class="{{ request()->routeIs('reports.orders') ? 'mm-active' : '' }}">Orders</a></li>
                    <li><a href="{{ route('reports.payments') }}" class="{{ request()->routeIs('reports.payments') ? 'mm-active' : '' }}">Payments</a></li>
                    <li><a href="{{ route('reports.expenses') }}" class="{{ request()->routeIs('reports.expenses') ? 'mm-active' : '' }}">Expenses</a></li>
                    <li><a href="{{ route('reports.customers') }}" class="{{ request()->routeIs('reports.customers') ? 'mm-active' : '' }}">Customers</a></li>
                    <li><a href="{{ route('reports.profit-loss') }}" class="{{ request()->routeIs('reports.profit-loss') ? 'mm-active' : '' }}">Profit & Loss</a></li>
                </ul>
            </li>
            <li><a class="has-arrow {{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'true' : 'false' }}">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Settings</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('settings.*') || request()->routeIs('users.*') ? 'true' : 'false' }}">
                    @hasPermission('users.view')
                    <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'mm-active' : '' }}">User Management</a></li>
                    @endhasPermission
                    <li><a href="{{ route('settings.order-statuses') }}" class="{{ request()->routeIs('settings.order-statuses*') ? 'mm-active' : '' }}">Order Statuses</a></li>
                    <li><a href="{{ route('settings.event-times') }}" class="{{ request()->routeIs('settings.event-times*') ? 'mm-active' : '' }}">Order Event Times</a></li>
                    <li><a href="{{ route('settings.order-types') }}" class="{{ request()->routeIs('settings.order-types*') ? 'mm-active' : '' }}">Order Types</a></li>
                    <li><a href="{{ route('settings.inventory-units') }}" class="{{ request()->routeIs('settings.inventory-units*') ? 'mm-active' : '' }}">Inventory Units</a></li>
                    <li><a href="{{ route('settings.equipment-categories') }}" class="{{ request()->routeIs('settings.equipment-categories*') ? 'mm-active' : '' }}">Equipment Categories</a></li>
                    <li><a href="{{ route('settings.equipment-statuses') }}" class="{{ request()->routeIs('settings.equipment-statuses*') ? 'mm-active' : '' }}">Equipment Statuses</a></li>
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