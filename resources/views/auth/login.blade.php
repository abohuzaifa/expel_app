@extends('layouts.app')
<?php
use App\Helpers\CommonHelper;
?>
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

            <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">{{ trans('lang.labeey') }}</span>
                </a>
              </div><!-- End Logo -->
              <div class="card mb-3">
              @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p>{{ $message }}</p>
                </div>
                @endif
                <div class="card-body">

                    <div class="pt-4 pb-2">
                        <h5 class="card-title text-center pb-0 fs-4">{{trans('lang.login_to_account')}}</h5>
                        <p class="text-center small">{{trans('lang.enter_username_password')}}</p>
                      </div>

                    <form  class="row g-3 needs-validation" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="col-12">
                            <label for="yourUsername" class="form-label">{{trans('lang.username')}}</label>
                            <div class="input-group has-validation">
                              <span class="input-group-text" id="inputGroupPrepend">@</span>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="yourPassword" class="form-label">{{trans('lang.password')}}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                        </div>

                        <div class="col-12">
                            <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{trans('lang.remember_me') }}
                                    </label>
                                </div>
                            </div>


                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ trans('lang.login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link small mb-0" href="{{ route('password.request') }}">
                                        {{ trans('lang.forgot_password') }}
                                    </a>
                                @endif
                            </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
