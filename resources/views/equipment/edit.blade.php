@extends('layout.default')

@section('title', 'Edit Equipment')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @include('error.alerts')
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Equipment</h4>
                </div>
                <div class="card-body">
                    {{-- Tips Section --}}
                    <x-tips-section>
                        <x-tip-item>
                            Use clear, descriptive names for equipment items to easily identify them in your inventory
                        </x-tip-item>
                        
                        <x-tip-item>
                            Total quantity represents the complete stock of equipment you own, while available quantity is what's currently ready for use
                        </x-tip-item>
                        
                        <x-tip-item>
                            Available quantity cannot exceed total quantity. The difference represents equipment that is currently assigned or unavailable
                        </x-tip-item>
                        
                        <x-tip-item>
                            Regularly update quantities when equipment is assigned, returned, or when new equipment is purchased
                        </x-tip-item>
                        
                        <x-tip-item>
                            Categorizing equipment helps organize your inventory and makes it easier to find and manage items
                        </x-tip-item>
                        
                        <x-tip-item>
                            Equipment status helps track the condition and availability of each item in your inventory
                        </x-tip-item>
                    </x-tips-section>
                    
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('equipment.update', $equipment) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            @include('equipment.form')
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Equipment</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
