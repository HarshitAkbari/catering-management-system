@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Vendor</h4>
                    <div class="card-tools">
                        <a href="{{ route('vendors.index') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('vendors.update', $vendor) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            @include('vendors.form')
                            <div class="row mt-4">
                                <div class="col-xl-8 col-lg-10 mx-auto">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('vendors.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Vendor</button>
                                    </div>
                                </div>
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
            Provide accurate vendor contact information to ensure smooth communication and order processing
        </x-tip-item>
        
        <x-tip-item>
            Vendor details are used throughout the system for purchase orders, inventory tracking, and supplier management
        </x-tip-item>
        
        <x-tip-item>
            Keep vendor information up-to-date to maintain accurate records and facilitate efficient operations
        </x-tip-item>
        
        <x-tip-item>
            Contact person field helps identify the primary point of contact for each vendor
        </x-tip-item>
    </x-tips-section>
</div>
@endsection
