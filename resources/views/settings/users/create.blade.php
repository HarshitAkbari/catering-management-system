@extends('layouts.app')

@section('title', $page_title ?? 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'User' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('error.alerts')
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('users.store') }}" novalidate>
                            @csrf
                            @include('settings.users._form')
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
            Assign appropriate roles to users based on their responsibilities (Admin, Manager, or Staff)
        </x-tip-item>
        
        <x-tip-item>
            Users can be assigned multiple roles for flexible permission management
        </x-tip-item>
        
        <x-tip-item>
            New users are automatically set to active status and can be activated/deactivated from the users list
        </x-tip-item>
        
        <x-tip-item>
            Passwords must meet security requirements. Users can change their password after first login
        </x-tip-item>
        
        <x-tip-item>
            Use the activate/deactivate feature to manage user access without deleting accounts
        </x-tip-item>
    </x-tips-section>
</div>

@endsection

