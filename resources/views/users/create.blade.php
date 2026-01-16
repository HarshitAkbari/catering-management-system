@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Create New User</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-alt alert-danger solid alert-dismissible fade show" role="alert">
                            <strong>There were errors with your submission:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="form-validation">
                        <form class="needs-validation" action="{{ route('users.store') }}" method="POST" novalidate>
                            @csrf

                            @php
                                $user = new \App\Models\User();
                            @endphp
                            @include('users._form')

                            <div class="row mt-4">
                                <div class="col-xl-8 col-lg-10 mx-auto">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Create User</button>
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

@section('scripts')
<script>
    (function () {
      'use strict'

      // Fetch all the forms we want to apply custom Bootstrap validation styles to
      var forms = document.querySelectorAll('.needs-validation')

      // Loop over them and prevent submission
      Array.prototype.slice.call(forms)
        .forEach(function (form) {
          form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
              event.preventDefault()
              event.stopPropagation()
            }

            form.classList.add('was-validated')
          }, false)
        })
    })()
</script>
@endsection

