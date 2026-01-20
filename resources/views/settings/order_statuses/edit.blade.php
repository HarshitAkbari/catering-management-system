@extends('layouts.app')

@section('title', 'Edit Order Status')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Order Status</h4>
                </div>
                <div class="card-body">
                    @include('error.alerts')
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('settings.order-statuses.update', $orderStatus) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            @include('settings.order_statuses.form')
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

