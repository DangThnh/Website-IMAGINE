@extends('layouts.layout')
@section('title', 'My Collections')
@section('styles')
<style>
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    main {
        flex: 1;
    }
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    .collections-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    /* Sửa lại phần CSS cho grid này */
    .collections-grid {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        gap: 20px;
        padding: 20px 0;
        overflow-x: auto;
        width: 100%;
        min-height: 320px;
    }
    .collection-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
        height: 280px;
        width: 200px;
        flex: 0 0 auto;
    }
    .collection-card:hover {
        transform: translateY(-5px);
    }
    .collection-thumbnail {
        height: 200px;
        width: 100%;
        display: grid;
        gap: 2px;
        overflow: hidden;
        background: #ddd;
    }
    .collection-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }
    .collection-thumbnail.single-image {
        grid-template-columns: 1fr;
        grid-template-rows: 200px;
    }
    .collection-thumbnail.two-images {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 200px;
    }
    .collection-thumbnail.three-images {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 100px 100px;
    }
    .collection-thumbnail.three-images img:nth-child(1) {
        grid-column: 1 / span 2;
        grid-row: 1;
    }
    .collection-thumbnail.three-images img:nth-child(2) {
        grid-column: 1;
        grid-row: 2;
    }
    .collection-thumbnail.three-images img:nth-child(3) {
        grid-column: 2;
        grid-row: 2;
    }
    .collection-thumbnail.four-images {
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 100px 100px;
    }
    .no-thumbnail {
        grid-column: 1 / -1;
        grid-row: 1 / -1;
        width: 100%;
        height: 200px;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
    }
    .collection-info {
        padding: 10px;
        text-align: center;
        width: 100%;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #f0f0f0;
    }
    .collection-info h3 {
        margin: 0 0 5px 0;
        font-size: 16px;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .image-count {
        font-size: 14px;
        color: #666;
    }
    .btn-primary, .back-button {
        padding: 10px 15px;
        background-color: #007BFF;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        display: inline-block;
    }
    .alert-success {
        padding: 15px;
        margin-bottom: 20px;
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        border-radius: 5px;
    }
    .image-count:before {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23666'%3E%3Cpath d='M19 5v14H5V5h14m0-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-4.86 8.86l-3 3.87L9 13.14 6 17h12l-3.86-5.14z'/%3E%3C/svg%3E") no-repeat center center;
        background-size: contain;
        margin-right: 5px;
    }
</style>
@endsection
@section('content')
<div class="container">
    <div class="collections-header">
        <h1>Collections</h1>
        @auth
            @if(Auth::id() == $userId)
                <a href="{{ route('collections.create') }}" class="btn btn-primary">Create New Collection</a>
            @endif
        @endauth
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($collections->isEmpty())
        <p>No collections found.</p>
    @else
        <div class="collections-grid">
            @foreach($collections as $collection)
                <a href="{{ route('collections.show', $collection->id) }}" class="collection-card" style="text-decoration: none; color: inherit;"> 
                    <div class="collection-thumbnail {{ $collection->images->count() == 1 ? 'single-image' : ($collection->images->count() == 2 ? 'two-images' : ($collection->images->count() == 3 ? 'three-images' : 'four-images')) }}">
                        @if($collection->images->isNotEmpty())
                            @foreach($collection->images->take(4) as $image)
                                <img src="{{ asset($image->path) }}" alt="{{ $collection->title }}">
                            @endforeach
                        @else
                            <div class="no-thumbnail">No Images</div>
                        @endif
                    </div>
                    <div class="collection-info">
                        <h3>{{ $collection->title }}</h3>
                        <span class="image-count">{{ $collection->image_count }} images</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ route('profile.show') }}" class="back-button">
            Return
        </a>
    </div>
</div>

@endsection