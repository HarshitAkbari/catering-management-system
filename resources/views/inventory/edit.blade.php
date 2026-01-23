@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Inventory Item</h4>
                    <div class="card-tools">
                        <a href="{{ route('inventory.index') }}" class="btn btn-default btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @include('error.alerts')
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('inventory.update', $inventoryItem) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            @include('inventory.form')
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
