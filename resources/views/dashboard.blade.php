@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <h1 class="page-title">Dashboard</h1>
    <p class="page-meta">Signed in as <strong>{{ auth()->user()->name }}</strong>.</p>
@endsection
