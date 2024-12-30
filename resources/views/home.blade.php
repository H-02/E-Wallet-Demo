@extends('layouts.app')

@section('content')
<div class="container text-center">
    <h1>Welcome to Mini E-Wallet System</h1>
    <p>Your one-stop solution for managing your wallet online.</p>
    <div class="mt-4">
        @guest
        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        @endguest

        @auth
        @if(Auth::user()->role === 'ADMIN')
        <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Go to Admin Panel</a>
        @else
        <a href="{{ route('user.dashboard') }}" class="btn btn-success">Go to User Dashboard</a>
        @endif
        @endauth
    </div>
</div>
@endsection
