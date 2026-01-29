@extends('layouts.app')

@section('title', $page_title ?? 'Create Staff Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Staff Role' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.staff-roles') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('settings.staff-roles.store') }}" novalidate>
                            @csrf
                            @include('settings.staff_roles.form')
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
            Use clear, descriptive names for staff roles (e.g., "Cook", "Waiter", "Manager", "Helper")
        </x-tip-item>
        
        <x-tip-item>
            Staff roles help categorize and manage your staff members
        </x-tip-item>
        
        <x-tip-item>
            You can activate/deactivate roles as needed from the staff roles list
        </x-tip-item>
        
        <x-tip-item>
            Deactivated roles won't appear in dropdowns but existing staff keep their role
        </x-tip-item>
    </x-tips-section>
</div>
@endsection

