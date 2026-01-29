@extends('layouts.app')

@section('title', $page_title ?? 'Create Event Time')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Event Time' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.event-times') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Tips Section --}}
                    <x-tips-section>
                        <x-tip-item>
                            Use clear, descriptive names for event times (e.g., "Morning", "Afternoon", "Evening")
                        </x-tip-item>
                        
                        <x-tip-item>
                            Event times help organize orders by time of day for better scheduling and planning
                        </x-tip-item>
                        
                        <x-tip-item>
                            You can activate/deactivate event times as needed from the event times list
                        </x-tip-item>
                        
                        <x-tip-item>
                            Deactivated event times won't appear in dropdowns but existing orders keep their event time
                        </x-tip-item>
                    </x-tips-section>
                    
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('settings.event-times.store') }}" novalidate>
                            @csrf
                            @include('settings.event_times.form')
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

