<div class="info dropdown show">
    {{-- <a href={{ route('profile') }} class="d-block">{{ ucwords(Auth::user()->name) }}</a> --}}
    <a class="d-block btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{ ucwords(Auth::user()->name) }}
    </a>

    <div class="dropdown-menu z" aria-labelledby="dropdownMenuLink">
        <a class="dropdown-item" href="#">Action</a>
        <a class="dropdown-item" href="#">Another action</a>
        <a class="dropdown-item" href="#">Something else here</a>
    </div>

</div>