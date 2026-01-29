@extends('layouts.app')

@section('title', $page_title ?? 'Create Order Status')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Order Status' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.order-statuses') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('settings.order-statuses.store') }}" novalidate>
                            @csrf
                            @include('settings.order_statuses.form')
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
            Use clear, descriptive names for order statuses (e.g., "Pending", "In Progress", "Completed")
        </x-tip-item>
        
        <x-tip-item>
            Order statuses help track the progress of orders through your workflow
        </x-tip-item>
        
        <x-tip-item>
            You can activate/deactivate statuses as needed from the order statuses list
        </x-tip-item>
        
        <x-tip-item>
            Deactivated statuses won't appear in dropdowns but existing orders keep their status
        </x-tip-item>
    </x-tips-section>
</div>
@endsection

