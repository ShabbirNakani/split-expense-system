<!-- Navbar -->
<!-- Left navbar links -->
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars mt-1"></i></a>
    </li>
    {{-- <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
    </li> --}}
    {{-- <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
    </li> --}}
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
    <!-- Navbar Search -->
    <li class="nav-item">
        <!-- Right Side Of Navbar -->
        <ul class="navbar-nav ms-auto">
            <!-- Authentication Links -->
            @guest

                @if (Route::has('login'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                @endif

                @if (Route::has('register'))
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                    </li>
                @endif
            @else
                <li class="nav-item ">
                    {{-- <a id="navbarDropdown" class="nav-link " href="#" role="button" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" v-pre>
                        {{ Auth::user()->name }}
                    </a> --}}

                </li>

                <li class="nav-item collapse navbar-collapse" id="navbar-list-4">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle mb-2" href="#" id="navbarDropdownMenuLink"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                @if (isset(Auth::user()->profile_pic))
                                    <img src={{ asset('images/' . Auth::user()->profile_pic) }} width="40"
                                        height="35" class="rounded-circle">
                                @else
                                    <img src={{ asset('images/' . 'default_user_image.jpg') }} width="40" height="35"
                                        class="rounded-circle">
                                @endif
                            </a>
                            {{-- carrd view for edit and logout --}}
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" style="min-width:220px">
                                <div class="card card-primary card-outline">
                                    <div class="card-body box-profile">
                                        {{-- image container --}}
                                        <div class="text-center">
                                            @if (isset(Auth::user()->profile_pic))
                                                {{-- if user has image --}}
                                                <img src="{{ asset('images/' . Auth::user()->profile_pic) }}"
                                                    class="profile-user-img img-fluid img-circle"
                                                    alt="User profile picture">
                                            @else
                                                {{-- default image --}}
                                                <img src="{{ asset('images/' . 'default_user_image.jpg') }}"
                                                    class="profile-user-img img-fluid img-circle"
                                                    alt="User profile picture">
                                            @endif
                                        </div>
                                        <h3 class="profile-username text-center">{{ ucwords(Auth::user()->name) }}</h3>
                                        <p class="text-muted text-center">{{ ucwords(Auth::user()->email) }}</p>
                                        <a href={{ route('edit.profile') }} class="btn btn-primary ">Edit Profile</a>
                                        <a class="btn btn-primary" href="{{ route('logout') }}"
                                            role="button"onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                                            {{ 'Logout' }}
                                        </a>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                            @csrf
                                        </form>
                                        </a>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
            @endguest
        </ul>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
            <i class="fas fa-expand-arrows-alt"></i>
        </a>
    </li>
</ul>
