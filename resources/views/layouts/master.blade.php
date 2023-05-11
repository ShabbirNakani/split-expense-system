<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head')
    @yield('pagewise-links')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css" integrity="sha256-mmgLkCYLUQbXn0B1SRqzHar6dCnv9oZFPEC1g1cwlkk=" crossorigin="anonymous" />


</head>

<body class="hold-transition sidebar-mini">

    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            @include('layouts.navbar')
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href={{ route('home') }} class="brand-link">
                <img src="{{asset('dist/img/AdminLTELogo.png')}}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light">Split Expense System</span>
            </a>

            <div class="sidebar">
                @include('layouts.sidebar')
            </div>
        </aside>
        <!-- /Main Sidebar Container -->

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="min-height: 1150px;">

            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page-content-title')</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href={{ route('home') }}>Home</a></li>
                                <li class="breadcrumb-item active">@yield('page-path')</li>
                            </ol>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            <!-- Main content -->
            <div class="content">
                <div class="content">
                    <div class="container-fluid">
                        <div class="row">
                            @yield('main-content')
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- footer --}}
        @include('layouts.footer')
        {{-- /footer --}}
        @include('layouts.script')
        @yield('page-script')

    </div>
    <!-- /Content Wrapper. Contains page content -->

</body>

</html>
