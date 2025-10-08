{{--author An--}}
@extends('layouts.layout')
@section('title', 'Edit Collection')
@section('content')
<div class="container">
    <h1>Edit Collection</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('collections.update', $collection->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ $collection->title }}" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ $collection->description }}</textarea>
        </div>
        
        <div class="form-group">
            <label for="is_public">Privacy:</label>
            <select class="form-control" id="is_public" name="is_public">
                <option value="1" {{ $collection->is_public ? 'selected' : '' }}>Public</option>
                <option value="0" {{ !$collection->is_public ? 'selected' : '' }}>Private</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">Update Collection</button>
        <a href="{{ route('collections.show', $collection->id) }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection