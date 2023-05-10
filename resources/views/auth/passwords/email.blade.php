@extends('authMIne.layouts.master')

@section('title', 'Forget Password')

@section('body')

    <body class="hold-transition login-page">
        <div class="login-box">
            <div class="login-logo">
                <a href={{ route('home') }}><b>Split Expense System
                    </b></a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        {{-- email input --}}
                        <div class="input-group mb-3">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="Email">

                            {{-- email logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            {{-- Error Handling --}}
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block">Request new password</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <p class="mt-3 mb-1">
                        <a href={{ route('login') }}>Login</a>
                    </p>
                    <p class="mb-0">
                        <a href={{ route('register') }} class="text-center">Register a new membership</a>
                    </p>
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
        <!-- /.login-box -->
        @include('authMIne.layouts.scripts')
    </body>
@endsection
