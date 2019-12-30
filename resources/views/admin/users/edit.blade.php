@extends('layouts.template')

@section('title', 'Edit user')

@section('main')
    <h1>Edit user: {{ $user->name }}</h1>
    @include('shared.alert')
    <form action="/admin/users/{{ $user->id }}" method="post">
        @method('put')
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   placeholder="Your name"
                   required
                   value="{{ old('name', $user->name ) }}">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   placeholder="Your email"
                   required
                   value="{{ old('email', $user->email) }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="checkbox" id="active" value="1" name="active" @if ($user->active or old('active')) checked @endif/> Active
            <input type="checkbox" id="admin" value="1" name="admin" @if ($user->admin or old('admin')) checked @endif/> Admin
        </div>
        <button type="submit" class="btn btn-success">Save user</button>
    </form>
@endsection
