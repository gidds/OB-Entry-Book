@extends('layouts.app')

@section('title', 'Management Login')

@section('content')
<section class="panel" style="max-width: 520px; margin: 0 auto;">
    <h1>Management Login</h1>
    <p class="muted">Management authentication is separate from controller PIN acknowledgement.</p>

    <form method="post" action="{{ route('login.store') }}">
        @csrf

        <label for="username">Username</label>
        <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required autofocus>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>

        <label style="font-weight:400">
            <input style="width:auto" type="checkbox" name="remember" value="1">
            Remember me
        </label>

        <button type="submit">Login</button>
    </form>
</section>
@endsection
