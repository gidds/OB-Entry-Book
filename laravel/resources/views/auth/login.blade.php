@extends('layouts.app')

@section('title', 'OB Book Login')

@section('content')
<div style="max-width:1000px;margin:0 auto">
    <h1>OB Book Login</h1>
    <p class="muted">Authentication is required before viewing or adding occurrence-book information.</p>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1rem;align-items:start">
        <section class="panel">
            <h2 style="margin-top:0">Controller</h2>
            <p class="muted">Use your controller PIN.</p>

            <form method="post" action="{{ route('login.controller') }}">
                @csrf

                <label for="pin">Controller PIN</label>
                <input id="pin" name="pin" type="password" inputmode="numeric" autocomplete="current-password" required autofocus>

                <button type="submit">Controller Login</button>
            </form>
        </section>

        <section class="panel">
            <h2 style="margin-top:0">Management</h2>
            <p class="muted">Managers and administrators use their username and password.</p>

            <form method="post" action="{{ route('login.store') }}">
                @csrf

                <label for="username">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" autocomplete="username" required>

                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>

                <label style="font-weight:400">
                    <input style="width:auto" type="checkbox" name="remember" value="1">
                    Remember me
                </label>

                <button type="submit">Management Login</button>
            </form>
        </section>
    </div>
</div>
@endsection
