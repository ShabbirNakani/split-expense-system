<!-- Sidebar user panel (optional) -->
{{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
    profile picture
    <div class="image">

        @if (isset(Auth::user()->profile_pic))
            if user has image
            <img src="{{ asset('images/' . Auth::user()->profile_pic) }}" class="img-circle elevation-2" alt="User Image">
        @else
            default image
            <img src="{{ asset('images/' . 'default_user_image.jpg') }}" class="img-circle elevation-2" alt="User Image">
        @endif

    </div>
    <div class="info text-primary dropdown">

        <a href={{ route('profile') }} class="d-block">{{ ucwords(Auth::user()->name) }}</a>
        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
            data-bs-toggle="dropdown" aria-expanded="false">{{ ucwords(Auth::user()->name) }}</a>

        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
        </ul>
    </div>
</div> --}}


<!-- Sidebar Menu -->
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href={{ route('home') }} class="nav-link ">
                <i class="nav-icon fas fa-home"></i>
                <p>
                    Dashboard
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href={{ route('groups.index') }} class="nav-link">
                <i class="nav-icon fas fa-grip-horizontal"></i>
                <p>
                    My Groups
                </p>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a href={{ route('group-detail') }} class="nav-link">
                <i class="nav-icon fas fa-info"></i>
                <p>
                    Group Detail
                </p>
            </a>
        </li> --}}
        <li class="nav-item">
            <a href={{ route('friends.index') }} class="nav-link">
                <i class="nav-icon fas fa-user-friends"></i>
                <p>
                    My Friends
                </p>
            </a>
        </li>
        {{-- <li class="nav-item">
            <a href={{ route('my-friends') }} class="nav-link">
                <i class="nav-icon fas fa-user-friends"></i>
                <p>
                    Profile
                </p>
            </a>
        </li> --}}
    </ul>
</nav>

{{-- <!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
  Launch static backdrop modal
</button>

{{-- <!-- Modal --> --}}
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Understood</button>
            </div>
        </div>
    </div>
</div>
