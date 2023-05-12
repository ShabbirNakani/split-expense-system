@extends('layouts.master')
@section('title', 'Dashboard')
@section('page-content-title', 'Dashboard')
@section('page-path', 'Dashboard')

@section('main-content')
    <div class="col-12">
    </div>

    <div class="col-12">
        <h1>
            welcome!! <span> {{ Auth::user()->name }}</span>

        </h1>
        {{-- <div class="card">
            <div class="card-body">
                <h3>
                    total Owe Till Date : {{ $total_owe }} RS
                </h3>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-xl-3 col-lg-6 mt-5">
                <div class="card l-bg-orange-dark">
                    <div class="card-statistic-3 p-4">
                        <div class="card-icon card-icon-large"><i class="fas fa-dollar-sign"></i></div>
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Total Amount You have spent In all Groups</h5>
                        </div>
                        <div class="row align-items-center mb-2 d-flex">
                            <div class="col-8">
                                <h2 class="d-flex align-items-center mb-0">
                                    {{ $total_owe }} Rs.
                                </h2>
                            </div>
                            {{-- <div class="col-4 text-right">
                                <span>2.5% <i class="fa fa-arrow-up"></i></span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-lg-6 mt-5">
                <div class="card l-bg-green-dark">
                    <div class="card-statistic-3 p-4">
                        <div class="card-icon card-icon-large"><i class="fas fa-donate"></i></div>
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Total Amount Spent On You By Friends</h5>
                        </div>
                        <div class="row align-items-center mb-2 d-flex">
                            <div class="col-8">
                                <h2 class="d-flex align-items-center mb-0">
                                    {{ $total_pay }} Rs.
                                </h2>
                            </div>
                            {{-- <div class="col-4 text-right">
                                <span>10% <i class="fa fa-arrow-up"></i></span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 mt-5">
                <div class="card l-bg-cherry">
                    <div class="card-statistic-3 p-4">
                        <div class="card-icon card-icon-large"><i class="far fa-credit-card"></i></div>
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Remaining Settelements</h5>
                        </div>
                        <div class="row align-items-center mb-2 d-flex">
                            <div class="col-8">
                                <h2 class="d-flex align-items-center mb-0">
                                    {{ $remainingAmount }}
                                </h2>
                            </div>
                            {{-- <div class="col-4 text-right">
                                <span>12.5% <i class="fa fa-arrow-up"></i></span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 mt-5">
                <div class="card l-bg-blue-dark">
                    <div class="card-statistic-3 p-4">
                        <div class="card-icon card-icon-large"><i class="fas fa-users"></i></div>
                        <div class="mb-4">
                            <h5 class="card-title mb-0">Groups You Are In</h5>
                        </div>
                        <div class="row align-items-center mb-2 d-flex">
                            <div class="col-8">
                                <h2 class="d-flex align-items-center mb-0">
                                    {{ $groupsCount }}
                                </h2>
                            </div>
                            {{-- <div class="col-4 text-right"> --}}
                            {{-- <span>9.23% <i class="fa fa-arrow-up"></i></span> --}}
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
