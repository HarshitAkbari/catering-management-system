@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Settings</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('settings.company-profile') }}" class="text-dark">
                        <h4 class="card-title mb-3">Company Profile</h4>
                        <p class="card-text">Manage company information and branding</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('settings.invoice-branding') }}" class="text-dark">
                        <h4 class="card-title mb-3">Invoice Branding</h4>
                        <p class="card-text">Configure invoice templates and branding</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('settings.event-types') }}" class="text-dark">
                        <h4 class="card-title mb-3">Event Types</h4>
                        <p class="card-text">Manage event categories</p>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('settings.notifications') }}" class="text-dark">
                        <h4 class="card-title mb-3">Notifications</h4>
                        <p class="card-text">Configure notification preferences</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

