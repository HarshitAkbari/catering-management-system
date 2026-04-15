@extends('layouts.app')

@section('title', $page_title ?? 'Create Event Menu Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Event Menu Item' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('orders.event-menu-items') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Tips Section --}}
                    <x-tips-section>
                        <x-tip-item>
                            Use clear, descriptive names for event menu items (e.g., "Chhas", "Papad", "Rotli")
                        </x-tip-item>
                        
                        <x-tip-item>
                            Event menu items help categorize the food items that can be selected when creating orders
                        </x-tip-item>
                        
                        <x-tip-item>
                            You can activate/deactivate event menu items as needed from the event menu items list
                        </x-tip-item>
                        
                        <x-tip-item>
                            Deactivated event menu items won't appear in dropdowns but existing orders keep their items
                        </x-tip-item>
                    </x-tips-section>
                    
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('orders.event-menu-items.store') }}" novalidate>
                            @csrf
                            @include('event_menu_items.form')
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


