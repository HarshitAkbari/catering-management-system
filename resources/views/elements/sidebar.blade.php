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
            <li><a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-file-text"></i>
                    <span class="nav-text">Orders</span>
                </a>
            </li>
            <li><a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Customers</span>
                </a>
            </li>
            <li><a href="{{ route('payments.index') }}" class="{{ request()->routeIs('payments.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-credit-card"></i>
                    <span class="nav-text">Payments</span>
                </a>
            </li>
            <li><a href="{{ route('inventory.index') }}" class="{{ request()->routeIs('inventory.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-box-seam"></i>
                    <span class="nav-text">Inventory</span>
                </a>
            </li>
            <li><a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-building"></i>
                    <span class="nav-text">Vendors</span>
                </a>
            </li>
            <li><a href="{{ route('equipment.index') }}" class="{{ request()->routeIs('equipment.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-tools"></i>
                    <span class="nav-text">Equipment</span>
                </a>
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
            <li><a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'mm-active' : '' }}" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Settings</span>
                </a>
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