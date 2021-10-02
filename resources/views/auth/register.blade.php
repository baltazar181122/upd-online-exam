@extends('layouts.app')

@section('content')
<div class="container maroon">
    <div class="row">
        <div class="col-md-4">
            <div class="">
                <div class="card-body">
                    <div class="row justify-content-center">
                        <img src="{{ asset('assets/images/updent.png')}}">
                       <h1 class="text-white"><strong>UP Dentistry</strong></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <br>
            <div class="card">
                <!-- <div class="card-header">{{ __('Register') }}</div> -->
                <div class="card-body">
                    <div class="alert alert-warning alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>	
                        <strong>This is one time registration only, once you already registered, this link will no longer valid, please be mindful on your registration, Thank you!</strong>
                    </div>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <input type="hidden"  name="code" value="{{ $code }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">{{ __('First Name') }}</label>
                                        <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>
                                        @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">{{ __('Last Name:') }}</label>
                                        <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name">
                                        @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('E-Mail Address') }}</label>
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email }}" required autocomplete="email" readonly>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">{{ __('Password') }}</label>
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Register') }}
                                        </button>
                                </div>
                            </div>
                        </div>
                       
                    </form>
                </div>
            </div>
            <br>
        </div>
    </div>
</div>
@endsection
