@extends('layouts.app')

@section('title', 'Invoice Branding')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Invoice Branding</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Invoice Branding</h4>
                </div>
                <div class="card-body">
                    <div class="basic-form">
                        <form action="{{ route('settings.invoice-branding.update') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Invoice Logo URL</label>
                                <input type="url" name="invoice_logo" value="{{ old('invoice_logo', $settings['invoice_logo'] ?? '') }}" class="form-control">
                                @error('invoice_logo')
                                    <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Footer Text</label>
                                <textarea name="invoice_footer_text" rows="3" class="form-control">{{ old('invoice_footer_text', $settings['invoice_footer_text'] ?? '') }}</textarea>
                                @error('invoice_footer_text')
                                    <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Terms & Conditions</label>
                                <textarea name="invoice_terms" rows="5" class="form-control">{{ old('invoice_terms', $settings['invoice_terms'] ?? '') }}</textarea>
                                @error('invoice_terms')
                                    <p class="text-danger small mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('settings.index') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Branding</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

