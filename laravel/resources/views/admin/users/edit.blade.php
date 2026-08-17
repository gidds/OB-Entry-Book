@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<section class="panel" style="max-width:760px;margin:0 auto">
    <div class="top-actions">
        <div>
            <h1>Edit User</h1>
            <div class="muted">Update {{ $managedUser->name }}. Leave password or PIN blank to keep the existing value.</div>
        </div>
        <a class="button" href="{{ route('admin.users.index') }}">Back to Users</a>
    </div>

    <form method="post" action="{{ route('admin.users.update', $managedUser) }}">
        @csrf
        @method('PUT')

        <label>Name
            <input name="name" value="{{ old('name', $managedUser->name) }}" required>
        </label>

        <label>Role
            <select name="role" style="width:100%;padding:.7rem;margin-top:.35rem">
                @foreach(['controller' => 'Controller', 'manager' => 'Manager', 'admin' => 'Admin'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $managedUser->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>Username <span class="muted">(required for manager/admin)</span>
            <input name="username" value="{{ old('username', $managedUser->username) }}">
        </label>

        <label>New Password <span class="muted">(leave blank to keep current, minimum 10 characters)</span>
            <input type="password" name="password" autocomplete="new-password">
        </label>

        <label>New Controller PIN <span class="muted">(leave blank to keep current, 4–10 digits)</span>
            <input type="password" inputmode="numeric" name="pin" autocomplete="new-password">
        </label>

        <button type="submit">Save Changes</button>
    </form>
</section>
@endsection
