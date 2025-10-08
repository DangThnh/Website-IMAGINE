{{--author An--}}
@extends('layouts.layout')
@section('title', 'Create Collection')
@section('content')
<div class="container">
    <h1>Create New Collection</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('collections.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="form-group">
            <label for="is_public">Privacy:</label>
            <select class="form-control" id="is_public" name="is_public">
                <option value="1" selected>Public</option>
                <option value="0">Private</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Create Collection</button>
        <a href="{{ route('collections.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection