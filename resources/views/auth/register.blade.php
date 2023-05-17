@extends('authMIne.layouts.master')

@section('title', 'Register')

@section('body')

    <body class="hold-transition register-page">
        <div class="register-box">
            <div class="register-logo">
                <a href={{ route('home') }}><b>Split Expense System</b></a>
            </div>

            <div class="card">
                <div class="card-body register-card-body">
                    <p class="login-box-msg">Register </p>

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id='register-form'>
                        @csrf
                        {{-- name  --}}
                        <div class="input-group mt-3">
                            <input type="text" class="form-control registerCommon @error('name') is-invalid @enderror" name="name"
                                value="{{ old('name') }}" autocomplete="name" autofocus placeholder="Full name">

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    {{-- name logo --}}
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                            {{-- error handling --}}
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- number  --}}
                        <div class="input-group mt-3">
                            <input type="tel" class="form-control registerCommon @error('number') is-invalid @enderror" name="number"
                                value="{{ old('number') }}" autocomplete="number" autofocus placeholder="Contact Number">

                            <div class="input-group-append">
                                <div class="input-group-text">
                                    {{-- number logo --}}
                                    <span class="fas fa-phone fa-sharp"></span>
                                </div>
                            </div>
                            {{-- error handling --}}
                            @error('number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- email --}}
                        <div class="input-group mt-3">
                            <input type="email" class="form-control registerCommon @error('email') is-invalid @enderror" name="email"
                                value="{{ old('email') }}" autocomplete="email" placeholder="Email">

                            {{-- email logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            {{-- error handling --}}
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- new password --}}
                        <div class="input-group mt-3">
                            <input type="password" class="form-control registerCommon @error('password') is-invalid @enderror"
                                name="password" autocomplete="new-password" placeholder="Password">
                            {{-- logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            {{-- error handling --}}
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- confirm new password --}}
                        <div class="input-group mt-3" >
                            <input type="password" class="form-control registerCommon" name="password_confirmation"
                                autocomplete="new-password" placeholder="Retype password">
                            {{-- logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        {{-- profile picture --}}
                        <div class="input-group mt-3 mb-3">
                            <input type="file" class="form-control registerCommon @error('Profilepic') is-invalid @enderror"
                                name="Profilepic" id="profile-pic-register" autocomplete="Profilepic" style="height: 45px;">

                            {{-- Profilepic logo --}}
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-image"></span>
                                </div>
                            </div>
                            {{-- error handling --}}
                            @error('Profilepic')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div id='img_contain'>
                            @if (isset(Auth::user()->profile_pic))
                                <img src={{ asset('images/' . Auth::user()->profile_pic) }} id="image-preview"
                                    class="profile-user-img img-fluid img-circle" alt="Profile Pic">
                            @else
                                <img src={{ asset('images/' . 'default_user_image.jpg') }} id="image-preview"
                                    class="profile-user-img img-fluid img-circle" alt="Profile Pic">
                            @endif
                        </div>
                        <div class="row mt-2">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                    <label for="agreeTerms">
                                        I agree to the <a href="#">terms</a>
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block" id="registerBtn">Register</button>
                            </div>
                            <!-- /.col -->
                        </div>



                    </form>

                    {{-- <div class="social-auth-links text-center">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i>
                        Sign up using Facebook
                    </a>
                    <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i>
                        Sign up using Google+
                    </a>
                </div> --}}

                    <a href={{ route('login') }} class="text-center">Already Registered</a>
                </div>
                <!-- /.form-box -->
            </div><!-- /.card -->
        </div>
        <!-- /.register-box -->
    </body>
    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
    {!! JsValidator::formRequest('App\Http\Requests\RegisterRequest', '#register-form') !!}
    <script>
        $(function() {
            // initially disable the register button
            $('#registerBtn').prop('disabled', true);
            let agreeTermsCheckbox = $('#agreeTerms');
            // console.log(agreeTermsCheckbox);
            $(agreeTermsCheckbox).on('change', function() {
                if ($(this).prop('checked') == true) {
                    $('#registerBtn').prop('disabled', false);
                } else {
                    $('#registerBtn').prop('disabled', true);
                }
            })


            // $("#register-form").validate({
            //     rules: {
            //         name: {
            //             required: true,
            //             maxlength: 200,
            //         },
            //         number: {
            //             required: true,
            //             minlength: 8,
            //         },
            //         email: {
            //             required: true,
            //             email: true,
            //             maxlength: 200,
            //             lettersonly: true,
            //         },
            //         password: {
            //             required: true,
            //         },
            //         password_confirmation: {
            //             required: true,
            //         },
            //         Profilepic: {
            //             required: true,
            //         },
            //     },
            //     messages: {
            //         title: "Title field is required.",
            //         discription: "Discription field is required.",
            //         "users[]": "Atleast one user is required."
            //     },
            //     submitHandler: function(form) {
            //         form.submit();
            //     }
            // });

        })
    </script>
    @include('layouts.script')
@endsection
