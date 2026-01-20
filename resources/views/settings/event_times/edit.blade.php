@extends('layouts.app')

@section('title', 'Edit Event Time')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Edit Event Time</h4>
                </div>
                <div class="card-body">
                    @include('error.alerts')
                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('settings.event-times.update', $eventTime) }}" method="POST" novalidate>
                            @csrf
                            @method('PUT')
                            @include('settings.event_times.form')
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


