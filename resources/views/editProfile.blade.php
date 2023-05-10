@extends('layouts.master')
@section('title', 'Edit Profile')
@section('page-content-title', 'Edit Profile')
@section('page-path', 'Edit Profile')

@section('main-content')

    <div class="col-6">
        <div class="card">
            {{-- <div class="card-header p-2"> --}}
            {{-- <ul class="nav nav-pills"> --}}
            {{-- <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Edit Profile</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#timeline" data-toggle="tab">Timeline</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li> --}}
            {{-- </ul> --}}
            {{-- </div> --}}
            <div class="card-body">
                <div class="tab-content">
                    <div class="container">
                        <h3 class="mb-4"> Edit Your Profile </h3>
                        <div class="row">
                            <div class="col">
                                <form method="POST" action="{{ route('update.profile') }}" enctype="multipart/form-data">
                                    @csrf
                                    {{-- name  --}}
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            name="name" value="{{ Auth::user()->name }}" required autocomplete="name"
                                            autofocus placeholder="Full name">

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
                                    <div class="input-group mb-3">
                                        <input type="tel" class="form-control @error('number') is-invalid @enderror"
                                            name="number" value="{{ Auth::user()->number }}" required autocomplete="number"
                                            autofocus placeholder="Contact Number">

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
                                    <div class="input-group mb-3">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            name="email" value="{{ Auth::user()->email }}" required autocomplete="email"
                                            placeholder="Email">

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
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
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
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" name="password_confirmation"
                                            autocomplete="new-password" placeholder="Retype password">
                                        {{-- logo --}}
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <div class="tab-content">
                    {{-- profile picture --}}
                    <div class="input-group mb-3">
                        <input type="file" class="form-control @error('Profilepic') is-invalid @enderror"
                            name="Profilepic" id="file-input" autocomplete="Profilepic" style="height: 45px;">

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
                    {{-- preview profile pic --}}
                    <div id='img_contain'>
                        @if (isset(Auth::user()->profile_pic))
                            <img src={{ asset('images/' . Auth::user()->profile_pic) }} id="image-preview"
                                class="profile-user-img img-fluid img-circle" alt="Profile Pic">
                        @else
                            <img src={{ asset('images/' . 'default_user_image.jpg') }} id="image-preview"
                                class="profile-user-img img-fluid img-circle" alt="Profile Pic">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- /.col -->
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-block" id="updateBtn">Update
            </button>
        </div>
        <!-- /.col -->
    </div>
    </form>

@endsection
