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
                    <div class="nav-item d-flex align-items-center">
                        <div class="input-group search-area">
                            <span class="input-group-text">
                                <a href="javascript:void(0)">
                                    <svg width="24" height="24" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M27.414 24.586L22.337 19.509C23.386 17.928 24 16.035 24 14C24 8.486 19.514 4 14 4C8.486 4 4 8.486 4 14C4 19.514 8.486 24 14 24C16.035 24 17.928 23.386 19.509 22.337L24.586 27.414C25.366 28.195 26.634 28.195 27.414 27.414C28.195 26.633 28.195 25.367 27.414 24.586ZM7 14C7 10.14 10.14 7 14 7C17.86 7 21 10.14 21 14C21 17.86 17.86 21 14 21C10.14 21 7 17.86 7 14Z" fill="var(--secondary)"/>
                                    </svg>
                                </a>
                            </span>
                            <input type="text" class="form-control" placeholder="Search here...">
                        </div>
						<ul>
							<li class="nav-item dropdown header-profile">
								<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									@if(auth()->check())
										<div class="header-profile-img" style="width: 20px; height: 20px; border-radius: 50%; background: var(--primary); display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 10px; font-weight: bold;">
											{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
										</div>
									@else
										<img src="images/profile/pic1.jpg" width="20" alt=""/>
									@endif
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									@if(auth()->check())
										<div class="dropdown-item">
											<strong>{{ auth()->user()->name ?? 'User' }}</strong>
											<small class="d-block text-muted">{{ auth()->user()->email ?? '' }}</small>
										</div>
										<div class="dropdown-divider"></div>
									@endif
									<form method="POST" action="{{ route('logout') }}">
										@csrf
										<button type="submit" class="dropdown-item ai-icon" style="border: none; background: none; width: 100%; text-align: left;">
											<svg xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
												<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
												<polyline points="16 17 21 12 16 7"></polyline>
												<line x1="21" y1="12" x2="9" y2="12"></line>
											</svg>
											<span class="ms-2">Logout</span>
										</button>
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
