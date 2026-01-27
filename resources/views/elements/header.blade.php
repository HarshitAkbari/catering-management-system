<!--**********************************
    Header start
***********************************-->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        {{ config('dz.name') }}
                    </div>
                </div>
                <div class="navbar-nav header-right">
                    <div class="profile-wrapper">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link profile-toggle d-flex align-items-center" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if(auth()->check())
                                        @php
                                            $user = auth()->user();
                                            $profileName = $user->name ?? 'User';
                                            $profileRole = $user->roles()->first()?->display_name ?? ucfirst($user->role ?? 'User');
                                        @endphp
                                        <div class="profile-info">
                                            <div class="d-flex flex-column text-end me-3">
                                                <span class="profile-name font-w600 text-dark text-truncate" title="{{ $profileName }}">{{ $profileName }}</span>
                                                <small class="profile-role text-muted text-truncate" title="{{ $profileRole }}">{{ $profileRole }}</small>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="profile-icon-wrapper">
                                        <div class="profile-icon bg-primary">
                                            <i class="bi bi-person-circle"></i>
                                        </div>
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end profile-dropdown">
                                    @if(auth()->check())
                                        <div class="dropdown-divider"></div>
                                        <a href="{{ route('profile.edit') }}" class="dropdown-item ai-icon">
                                            <i class="bi bi-person text-primary"></i>
                                            <span class="ms-2">Profile</span>
                                        </a>
                                        <a href="{{ route('change-password') }}" class="dropdown-item ai-icon">
                                            <i class="bi bi-key text-success"></i>
                                            <span class="ms-2">Change Password</span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                    @endif
                                    <a href="{{ route('logout') }}" class="dropdown-item ai-icon logout-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right text-danger"></i>
                                        <span class="ms-2">Logout</span>
                                    </a>
                                    <form id="logout-form" method="POST" action="{{ route('logout') }}" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>
<!--**********************************
    Header end
***********************************-->
