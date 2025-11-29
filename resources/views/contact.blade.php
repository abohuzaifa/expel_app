@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
                <a href="/" class="logo d-flex align-items-center w-auto">
                    <img src="/assets/img/logo.png" alt="">
                    <span class="d-none d-lg-block">{{ trans('lang.labeey') }}</span>
                </a>
            </div>
            <!-- End Logo -->

            <div class="card mb-3">

                @if(session('success'))
                <div class="alert alert-success text-center">
                    {{ session('success') }}
                </div>
                @endif

                <div class="card-body">

                    <div class="pt-4 pb-2">
                        <h5 class="card-title text-center pb-0 fs-4">
                            {{ trans('lang.contact_us') }}
                        </h5>
                        <p class="text-center small">
                            {{ trans('lang.fill_form_contact') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('contact.store') }}" class="row g-3 needs-validation">
                        @csrf

                        <div class="col-12">
                            <label class="form-label">{{ trans('lang.full_name') }}</label>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" required>

                            @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ trans('lang.email') }}</label>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" required>

                            @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ trans('lang.subject') }}</label>
                            <input type="text" name="subject"
                                class="form-control @error('subject') is-invalid @enderror"
                                value="{{ old('subject') }}" required>

                            @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">{{ trans('lang.message') }}</label>
                            <textarea name="message" rows="4"
                                class="form-control @error('message') is-invalid @enderror"
                                required>{{ old('message') }}</textarea>

                            @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">
                                {{ trans('lang.send_message') }}
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
