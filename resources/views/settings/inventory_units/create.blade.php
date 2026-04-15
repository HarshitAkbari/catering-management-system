@extends('layouts.app')

@section('title', $page_title ?? 'Create Inventory Unit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Inventory Unit' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.inventory-units') }}" class="btn btn-dark btn-sm">
                            <i class="bi bi-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Tips Section --}}
                    <x-tips-section>
                        <x-tip-item>
                            Enter the full name (e.g., "Kilogram", "Liter", "Piece") and short name (e.g., "kg", "L", "pc") for inventory units
                        </x-tip-item>
                        
                        <x-tip-item>
                            Full name is displayed in dropdowns and forms, while short name is used in tables and compact displays
                        </x-tip-item>
                        
                        <x-tip-item>
                            Inventory units help standardize measurements across your inventory items
                        </x-tip-item>
                        
                        <x-tip-item>
                            You can activate/deactivate units as needed from the inventory units list
                        </x-tip-item>
                        
                        <x-tip-item>
                            Deactivated units won't appear in dropdowns but existing inventory items keep their unit
                        </x-tip-item>
                    </x-tips-section>
                    
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('settings.inventory-units.store') }}" novalidate>
                            @csrf
                            @include('settings.inventory_units.form')
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

