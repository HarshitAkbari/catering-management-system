@extends('layouts.app')

@section('title', 'Activity')

@section('page_content')
<!-- Activity Feed Row -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0 flex-wrap">
                <div class="course-details-tab style-2 tab-lg">
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active nt-unseen" id="nav-following-tab" data-bs-toggle="tab" data-bs-target="#nav-following" type="button" role="tab" aria-controls="nav-following" aria-selected="true">Following</button>
                            <button class="nav-link" id="nav-you-tab" data-bs-toggle="tab" data-bs-target="#nav-you" type="button" role="tab" aria-controls="nav-you" aria-selected="false">You</button>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade active show" id="nav-following" role="tabpanel" aria-labelledby="nav-following-tab">
                        <div id="DZ_W_TimeLine11" class="widget-timeline style-3" data-tab="following">
                            @if(!empty($activities['following']['today']) || !empty($activities['following']['yesterday']))
                                @if(!empty($activities['following']['today']))
                                    <h3 class="mt-3">Today</h3>
                                    <ul class="timeline-active" data-date-filter="today" data-offset="{{ count($activities['following']['today']) }}">
                                        @foreach($activities['following']['today'] as $activity)
                                            <li class="d-flex align-items-baseline" data-activity-id="{{ $activity['id'] }}">
                                                <h4 class="font-w400 time">{{ $activity['time'] }}</h4>
                                                <div class="panel">
                                                    <a class="timeline-panel text-muted d-flex align-items-center" href="#">
                                                        <div class="badge badge-xl {{ $activity['badge_color'] }}">{{ $activity['user_initials'] }}</div>
                                                        <h4 class="mb-0"><strong>{{ $activity['user_name'] }}</strong> {{ $activity['description'] }}</h4>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="loading-indicator-today text-center py-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading more activities...</span>
                                    </div>
                                @endif
                                @if(!empty($activities['following']['yesterday']))
                                    <h3 class="mt-3">Yesterday</h3>
                                    <ul class="timeline-active" data-date-filter="yesterday" data-offset="{{ count($activities['following']['yesterday']) }}">
                                        @foreach($activities['following']['yesterday'] as $activity)
                                            <li class="d-flex align-items-baseline" data-activity-id="{{ $activity['id'] }}">
                                                <h4 class="font-w400 time">{{ $activity['time'] }}</h4>
                                                <div class="panel">
                                                    <a class="timeline-panel text-muted d-flex align-items-center" href="#">
                                                        <div class="badge badge-xl {{ $activity['badge_color'] }}">{{ $activity['user_initials'] }}</div>
                                                        <h4 class="mb-0"><strong>{{ $activity['user_name'] }}</strong> {{ $activity['description'] }}</h4>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="loading-indicator-yesterday text-center py-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading more activities...</span>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5 text-muted">
                                    <p>No activities from other users</p>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="tab-pane fade" id="nav-you" role="tabpanel" aria-labelledby="nav-you-tab">
                        <div id="DZ_W_TimeLine12" class="widget-timeline style-3" data-tab="you">
                            @if(!empty($activities['you']['today']) || !empty($activities['you']['yesterday']))
                                @if(!empty($activities['you']['today']))
                                    <h3 class="mt-3">Today</h3>
                                    <ul class="timeline-active" data-date-filter="today" data-offset="{{ count($activities['you']['today']) }}">
                                        @foreach($activities['you']['today'] as $activity)
                                            <li class="d-flex align-items-baseline" data-activity-id="{{ $activity['id'] }}">
                                                <h4 class="font-w400 time">{{ $activity['time'] }}</h4>
                                                <div class="panel">
                                                    <a class="timeline-panel text-muted d-flex align-items-center" href="#">
                                                        <div class="badge badge-xl {{ $activity['badge_color'] }}">{{ $activity['user_initials'] }}</div>
                                                        <h4 class="mb-0"><strong>{{ $activity['user_name'] }}</strong> {{ $activity['description'] }}</h4>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="loading-indicator-today text-center py-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading more activities...</span>
                                    </div>
                                @endif
                                @if(!empty($activities['you']['yesterday']))
                                    <h3 class="mt-3">Yesterday</h3>
                                    <ul class="timeline-active" data-date-filter="yesterday" data-offset="{{ count($activities['you']['yesterday']) }}">
                                        @foreach($activities['you']['yesterday'] as $activity)
                                            <li class="d-flex align-items-baseline" data-activity-id="{{ $activity['id'] }}">
                                                <h4 class="font-w400 time">{{ $activity['time'] }}</h4>
                                                <div class="panel">
                                                    <a class="timeline-panel text-muted d-flex align-items-center" href="#">
                                                        <div class="badge badge-xl {{ $activity['badge_color'] }}">{{ $activity['user_initials'] }}</div>
                                                        <h4 class="mb-0"><strong>{{ $activity['user_name'] }}</strong> {{ $activity['description'] }}</h4>
                                                    </a>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="loading-indicator-yesterday text-center py-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <span class="ms-2">Loading more activities...</span>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5 text-muted">
                                    <p>No activities found</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Infinite Scroll for Activity Log
    (function() {
        const scrollThreshold = 200; // Load when 200px from bottom
        let loadingStates = {
            'following-today': false,
            'following-yesterday': false,
            'you-today': false,
            'you-yesterday': false
        };
        let hasMoreData = {
            'following-today': true,
            'following-yesterday': true,
            'you-today': true,
            'you-yesterday': true
        };

        function getLoadingKey(tab, dateFilter) {
            return `${tab}-${dateFilter}`;
        }

        function loadMoreActivities(tab, dateFilter, ulElement) {
            const loadingKey = getLoadingKey(tab, dateFilter);
            
            // Prevent multiple simultaneous requests or if no more data
            if (loadingStates[loadingKey] || !hasMoreData[loadingKey]) {
                return;
            }

            const offset = parseInt(ulElement.getAttribute('data-offset')) || 0;
            const loadingIndicator = ulElement.parentElement.querySelector(`.loading-indicator-${dateFilter}`);
            
            // Show loading indicator
            if (loadingIndicator) {
                loadingIndicator.style.display = 'block';
            }
            
            loadingStates[loadingKey] = true;

            // Make AJAX request
            fetch(`{{ route('activities.load-more') }}?tab=${tab}&date_filter=${dateFilter}&offset=${offset}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.activities && data.activities.length > 0) {
                    // Append new activities
                    data.activities.forEach(activity => {
                        // Check if activity already exists
                        const existingActivity = ulElement.querySelector(`[data-activity-id="${activity.id}"]`);
                        if (existingActivity) {
                            return; // Skip if already exists
                        }

                        const li = document.createElement('li');
                        li.className = 'd-flex align-items-baseline';
                        li.setAttribute('data-activity-id', activity.id);
                        
                        li.innerHTML = `
                            <h4 class="font-w400 time">${activity.time}</h4>
                            <div class="panel">
                                <a class="timeline-panel text-muted d-flex align-items-center" href="#">
                                    <div class="badge badge-xl ${activity.badge_color}">${activity.user_initials}</div>
                                    <h4 class="mb-0"><strong>${activity.user_name}</strong> ${activity.description}</h4>
                                </a>
                            </div>
                        `;
                        
                        ulElement.appendChild(li);
                    });

                    // Update offset
                    const newOffset = offset + data.activities.length;
                    ulElement.setAttribute('data-offset', newOffset);

                    // Update has more data flag
                    hasMoreData[loadingKey] = data.has_more;

                    // Hide loading indicator if no more activities
                    if (!data.has_more && loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }
                } else {
                    // No more activities
                    hasMoreData[loadingKey] = false;
                    if (loadingIndicator) {
                        loadingIndicator.style.display = 'none';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading activities:', error);
                if (loadingIndicator) {
                    loadingIndicator.style.display = 'none';
                }
            })
            .finally(() => {
                loadingStates[loadingKey] = false;
            });
        }

        function checkScrollPosition(timelineElement) {
            if (!timelineElement) return;

            const tab = timelineElement.getAttribute('data-tab');
            const ulElements = timelineElement.querySelectorAll('ul.timeline-active');

            ulElements.forEach(ulElement => {
                const dateFilter = ulElement.getAttribute('data-date-filter');
                if (!dateFilter) return;

                const loadingKey = getLoadingKey(tab, dateFilter);
                
                // Check if we're near the bottom
                const rect = ulElement.getBoundingClientRect();
                const windowHeight = window.innerHeight || document.documentElement.clientHeight;
                const distanceFromBottom = windowHeight - rect.bottom;

                if (distanceFromBottom <= scrollThreshold && hasMoreData[loadingKey] && !loadingStates[loadingKey]) {
                    loadMoreActivities(tab, dateFilter, ulElement);
                }
            });
        }

        function setupInfiniteScroll(timelineElement) {
            if (!timelineElement) return;

            // Use IntersectionObserver for better performance
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const tab = timelineElement.getAttribute('data-tab');
                        const ulElements = timelineElement.querySelectorAll('ul.timeline-active');
                        
                        ulElements.forEach(ulElement => {
                            const dateFilter = ulElement.getAttribute('data-date-filter');
                            if (dateFilter) {
                                const loadingKey = getLoadingKey(tab, dateFilter);
                                if (hasMoreData[loadingKey] && !loadingStates[loadingKey]) {
                                    loadMoreActivities(tab, dateFilter, ulElement);
                                }
                            }
                        });
                    }
                });
            }, {
                rootMargin: `${scrollThreshold}px`
            });

            // Observe the timeline container
            observer.observe(timelineElement);

            // Also set up scroll listener as fallback
            let scrollTimeout;
            window.addEventListener('scroll', function() {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(function() {
                    checkScrollPosition(timelineElement);
                }, 100);
            }, { passive: true });
        }

        // Initialize infinite scroll for both tabs
        document.addEventListener('DOMContentLoaded', function() {
            const followingTimeline = document.getElementById('DZ_W_TimeLine11');
            const youTimeline = document.getElementById('DZ_W_TimeLine12');

            setupInfiniteScroll(followingTimeline);
            setupInfiniteScroll(youTimeline);

            // Re-initialize when tab changes
            const tabButtons = document.querySelectorAll('#nav-tab button[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('shown.bs.tab', function() {
                    const targetId = this.getAttribute('data-bs-target');
                    const timelineElement = document.querySelector(targetId + ' .widget-timeline');
                    if (timelineElement) {
                        setupInfiniteScroll(timelineElement);
                    }
                });
            });
        });
    })();
</script>
@endsection

