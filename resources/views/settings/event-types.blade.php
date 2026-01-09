@extends('layouts.app')

@section('title', 'Event Types')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Event Types</a></li>
        </ol>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Event Types</h4>
                    <button onclick="document.getElementById('event-type-form').classList.toggle('d-none')" class="btn btn-primary btn-sm">Add Event Type</button>
                </div>
                <div class="card-body">
                    <div id="event-type-form" class="d-none mb-4">
                        <div class="basic-form">
                            <form action="{{ route('settings.event-types.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" required class="form-control">
                                        @error('name')
                                            <p class="text-danger small mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" name="display_order" value="0" min="0" class="form-control">
                                    </div>
                                    <div class="mb-3 col-md-4">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary w-100">Create</button>
                                    </div>
                                    <div class="mb-3 col-md-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" rows="2" class="form-control"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="datatable table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>Name</strong></th>
                                    <th><strong>Description</strong></th>
                                    <th><strong>Status</strong></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($eventTypes as $type)
                                    <tr>
                                        <td><strong>{{ $type->name }}</strong></td>
                                        <td>{{ $type->description ?? '-' }}</td>
                                        <td>
                                            @if($type->is_active)
                                                <span class="badge light badge-success">Active</span>
                                            @else
                                                <span class="badge light badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <form action="{{ route('settings.event-types.destroy', $type) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger shadow btn-xs sharp" onclick="return confirm('Are you sure?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No event types found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

