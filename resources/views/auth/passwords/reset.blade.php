@extends('authMIne.layouts.master')

@section('title', 'Reset Password')

@section('body')

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href={{ route('home') }}><b>Split Expense System</b></a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">You are only one step a way from your new password, recover your password now.
                    </p>

                    <form method="POST" action="{{ route('password.update') }}" id="reset-password-form">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        {{-- email --}}
                        <div class="input-group mt-3">
                            <input id="email" type="email" class="form-control resetCommon @error('email') is-invalid @enderror"
                                name="email" value="{{ $email ?? old('email') }}" autocomplete="email" autofocus>
                            {{-- email logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            {{-- error handling --}}
                            {{-- @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror --}}
                        </div>
                        {{-- password --}}
                        <div class="input-group mt-3">
                            <input id="password" type="password"
                                class="form-control  resetCommon @error('password') is-invalid @enderror" name="password"
                                autocomplete="new-password" placeholder="Password">
                            {{-- password logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            {{-- error --}}
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- confirm password --}}
                        <div class="input-group mt-3 mb-3">
                            <input id="password-confirm" type="password" class="form-control resetCommon" name="password_confirmation"
                                autocomplete="new-password" placeholder="Confirm Password">
                            {{-- logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">Change password</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <p class="mt-3 mb-1">
                        <a href={{ route('login') }}>Login</a>
                    </p>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /.login-box -->
        @include('authMIne.layouts.scripts')
    </body>
@endsection
@section('pagewise-script')
    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest', '#reset-password-form') !!}
@endsection
