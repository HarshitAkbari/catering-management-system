@extends('layouts.app')

@section('title', 'Edit Staff Role')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Staff Role</h4>
                    <div class="card-tools">
                        <a href="{{ route('settings.staff-roles') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('settings.staff-roles.update', $staffRole) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
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
</div>
@endsection

