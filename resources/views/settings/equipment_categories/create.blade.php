@extends('layouts.app')

@section('title', $page_title ?? 'Create Equipment Category')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Add {{ $page_title ?? 'Equipment Category' }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('settings.equipment-categories') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('error.alerts')
                    <div class="form-validation">
                        <form class="needs-validation" method="POST" action="{{ route('settings.equipment-categories.store') }}" novalidate>
                            @csrf
                            @include('settings.equipment_categories.form')
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
            Use clear, descriptive names for equipment categories (e.g., "Tables", "Chairs", "Linens")
        </x-tip-item>
        
        <x-tip-item>
            Equipment categories help organize and manage your catering equipment inventory
        </x-tip-item>
        
        <x-tip-item>
            You can activate/deactivate categories as needed from the equipment categories list
        </x-tip-item>
        
        <x-tip-item>
            Deactivated categories won't appear in dropdowns but existing equipment keeps its category
        </x-tip-item>
    </x-tips-section>
</div>
@endsection

