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
    </div>
@endsection
