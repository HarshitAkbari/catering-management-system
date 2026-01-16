@extends('layouts.app')

@section('title', 'Inventory')

@section('page_content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Inventory</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Unit</th>
                                <th>Current Stock</th>
                                <th>Minimum Stock</th>
                                <th>Price/Unit</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventoryItems as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->unit }}</td>
                                    <td>{{ number_format($item->current_stock, 2) }}</td>
                                    <td>{{ number_format($item->minimum_stock, 2) }}</td>
                                    <td>₹{{ number_format($item->price_per_unit, 2) }}</td>
                                    <td>
                                        @if($item->isLowStock())
                                            <span class="badge badge-danger">Low Stock</span>
                                        @else
                                            <span class="badge badge-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('inventory.show', $item) }}" class="btn btn-primary btn-sm" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('inventory.edit', $item) }}" class="btn btn-info btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Delete" onclick="return confirm('Are you sure?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <p class="text-muted">No inventory items found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($inventoryItems, 'links'))
                    <div class="mt-3">
                        {{ $inventoryItems->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
