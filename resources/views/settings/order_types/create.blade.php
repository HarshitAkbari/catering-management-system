@extends('layouts.app')

@section('title', $page_title ?? 'Create Order Type')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Order Type' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.order-types') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('settings.order-types.store') }}" novalidate>
                            @csrf
                            @include('settings.order_types.form')
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tips Section --}}
    <x-tips-section>
        <x-tip-item>
            Use clear, descriptive names for order types (e.g., "Full Service", "Preparation Only", "Delivery Only")
        </x-tip-item>
        
        <x-tip-item>
            Order types help categorize and manage different service levels for your catering orders
        </x-tip-item>
        
        <x-tip-item>
            You can activate/deactivate order types as needed from the order types list
        </x-tip-item>
        
        <x-tip-item>
            Deactivated order types won't appear in dropdowns but existing orders keep their type
        </x-tip-item>
    </x-tips-section>
</div>
@endsection

