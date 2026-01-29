@extends('layouts.app')

@section('title', $page_title ?? 'Add Staff')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Staff Member' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('staff.index') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('staff.store') }}" novalidate>
                            @csrf
                            @include('staff.form')
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
            Use clear, descriptive names for staff members to easily identify them in the system
        </x-tip-item>
        
        <x-tip-item>
            Assign appropriate roles to staff members based on their responsibilities and skills
        </x-tip-item>
        
        <x-tip-item>
            Provide accurate contact information (phone and email) for effective communication
        </x-tip-item>
        
        <x-tip-item>
            You can update staff status and information later from the staff list
        </x-tip-item>
    </x-tips-section>
</div>
@endsection

