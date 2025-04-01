@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="font-weight-light my-2">{{ __('Email Verification') }}</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <div class="verification-icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <p class="mb-4">
                            {{ __('We have sent a verification code to your email. Please enter the code below to complete your login.') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('2fa.verify') }}">
                        @csrf

                        <div class="mb-4 text-center">
                            <div class="verification-code-input">
                                <input id="verification_code" type="text" class="form-control form-control-lg @error('verification_code') is-invalid @enderror" name="verification_code" required autofocus maxlength="6" placeholder="000000">
                            </div>
                            @error('verification_code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('Verify') }}
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-light">
                    <div class="small">
                        <a href="{{ route('2fa.resend') }}" class="link-primary">{{ __('Didn\'t receive the code? Resend') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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

    .verification-icon {
        font-size: 3rem;
        color: #0d6efd;
        margin-bottom: 1rem;
    }

    .verification-code-input {
        max-width: 250px;
        margin: 0 auto;
    }

    .verification-code-input input {
        letter-spacing: 0.5rem;
        font-size: 2rem;
        text-align: center;
        font-weight: 600;
        padding: 0.5rem;
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
