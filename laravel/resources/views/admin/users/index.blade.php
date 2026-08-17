@extends('layouts.app')
@section('title', 'User Administration')
@section('content')
<div class="top-actions">
    <div>
        <h1>User Administration</h1>
        <div class="muted">Create and edit controllers, managers and administrators.</div>
    </div>
</div>
<div class="dashboard-grid">
    <section class="panel">
        <h2>Create user</h2>
        <form method="post" action="{{ route('admin.users.store') }}">
            @csrf
            <label>Name<input name="name" value="{{ old('name') }}" required></label>
            <label>Role
                <select name="role" style="width:100%;padding:.7rem;margin-top:.35rem">
                    <option value="controller">Controller</option>
                    <option value="manager">Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </label>
            <label>Username <span class="muted">(manager/admin)</span><input name="username" value="{{ old('username') }}"></label>
            <label>Password <span class="muted">(manager/admin, minimum 10 characters)</span><input type="password" name="password"></label>
            <label>Controller PIN <span class="muted">(controller, 4–10 digits)</span><input type="password" inputmode="numeric" name="pin"></label>
            <button type="submit">Create User</button>
        </form>
    </section>
    <section class="panel">
        <h2>Existing users</h2>
        @foreach($users as $user)
            <div class="entry">
                <strong>{{ $user->name }}</strong>
                <div>{{ ucfirst($user->role) }}</div>
                <div class="muted">{{ $user->username ?: 'PIN identity only' }}</div>
                <a class="button" href="{{ route('admin.users.edit', $user) }}">Edit</a>
            </div>
        @endforeach
    </section>
</div>
@endsection
