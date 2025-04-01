@extends('layouts.app')

@section('content')
<div class="container">
  HOLA
</div>
@endsection

@section('styles')
<style>
    body {
        background: linear-gradient(to right, #74ebd5, #ACB6E5);
    }

    .card {
        border-radius: 1rem;
        overflow: hidden;
    }

    .card-header {
        border-bottom: 0;
        border-radius: 0;
        padding: 1.5rem 0;
    }

    .form-floating > .form-control {
        height: calc(3.5rem + 2px);
        padding: 1rem 0.75rem;
    }

    .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .btn-primary {
        padding: 0.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
</style>
@endsection
