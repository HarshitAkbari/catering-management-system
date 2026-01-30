@extends('layouts.app')

@section('title', $page_title ?? 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit {{ $page_title ?? 'User' }}</h4>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-dark btn-xs">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('users.update', $user) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')

                            @include('settings.users.form')

                            <div class="row mt-4">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Settings Deactivation Modal --}}
<x-settings-deactivation-modal 
    modal-id="user-deactivation-modal"
    setting-type="user"
    form-method="POST"
    csrf-method="PATCH"
/>

{{-- Settings Activation Modal --}}
<x-settings-activation-modal 
    modal-id="user-activation-modal"
    setting-type="user"
    form-method="POST"
    csrf-method="PATCH"
/>
@endsection

