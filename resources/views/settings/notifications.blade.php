@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Notifications</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Notification Settings</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('settings.notifications.update') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                    <input type="checkbox" class="form-check-input" name="sms_enabled" value="1" id="sms_enabled" {{ ($settings['sms_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sms_enabled">SMS Notifications</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                    <input type="checkbox" class="form-check-input" name="email_enabled" value="1" id="email_enabled" {{ ($settings['email_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="email_enabled">Email Notifications</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                    <input type="checkbox" class="form-check-input" name="low_stock_alert" value="1" id="low_stock_alert" {{ ($settings['low_stock_alert'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="low_stock_alert">Low Stock Alerts</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check custom-checkbox mb-3 checkbox-primary">
                                    <input type="checkbox" class="form-check-input" name="payment_reminder" value="1" id="payment_reminder" {{ ($settings['payment_reminder'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_reminder">Payment Reminders</label>
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('settings.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Settings</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

