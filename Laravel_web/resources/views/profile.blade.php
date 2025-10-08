@extends('layouts.layout')
@section('title', $user->name . ' profile')
@section('content')

@auth
<style>
    /* Move inline styles to CSS block for better organization */
    .user-name {
        color: white;
        position: absolute;
        right: 30px;
        top: 30px;
        font-size: 18px;
        z-index: 1000;
    }

    .chat-with-artist {
        border: none;
        background: none; /* Make background transparent */
        padding: 0; /* Remove padding */
    }

    .chat-with-artist > div {
        color: #007BFF;
        transition: color 0.3s ease; /* Smooth color transition on hover */
    }

    .chat-with-artist > div:hover {
        color: #0b59ad;
    }
</style>
<div class="user-name">{{ auth()->user()->name }}</div>
@endauth

    <link rel="stylesheet" href="{{ asset('/assets/css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('/assets/css/button.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Profile - {{ $user->name }}</title>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-info">
                <h1>{{ $user->name }}</h1>
                <p class="joined-date">Joined: {{ $user->created_at->format('d M Y') }}</p>
                <p class="profile-description">Explore the artistic world of {{ $user->name }}. Browse collections and individual images.</p>
            </div>
            <div class="profile-actions">
                @auth
                @if (auth()->id() == $user->id)
                <a href="{{ route('collections.index', $user->id) }}" class="button collection-button"> {{--An--}}
                    <i class="fa-regular fa-folder-open"></i> View Collections
                </a>
                @endif
                @endauth

                @if (auth()->id() !== $user->id)
                <form action="{{ route('room.create') }}" method="POST" class="chat-form">
                    @csrf
                    <input type="hidden" name="artist_id" value="{{ $user->id }}">
                    <button type="submit" class="chat-with-artist button secondary-button">
                        <div>
                            <i class="fa-solid fa-comments"></i> Chat
                        </div>
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="profile-images-section">
            <h2>Images by {{ $user->name }}</h2>
            <div class="profile-images">
                @if ($images->isEmpty())
                    <p>User has not uploaded any images yet.</p>
                @else
                <div class="image-grid">
                    @foreach ($images as $image)
                    <div class="image-item">
                        <a href="{{ url('/images', $image->id) }}">
                            <img src="{{ asset($image->path) }}" alt="{{ $image->filename }}">
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>


        <div class="back-button-container">
            <a href="javascript:history.back()" class="button back-button">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

@endsection




{{-- @extends('layouts.layout')
@section('title', $user->name . ' profile')
@section('content')

@auth
<style>
    .user-name {
        color: white; /* Màu chữ trắng */
        position: absolute; /* Đặt vị trí tuyệt đối */
        right: 30px; /* Cách cạnh phải 20px */
        top: 30px; /* Cách cạnh trên 20px */
        font-size: 18px; /* Kích thước chữ */
        z-index: 1000; /* Đảm bảo nó nằm trên các phần tử khác */
    }

    .chat-with-artist {
        border: none;
        background: white;
    }

    .chat-with-artist > div {
        color: #007BFF;
    }

    .chat-with-artist > div:hover {
        color: #0b59ad;
    }
</style>
<div class="user-name">{{ auth()->user()->name }}</div>
@endauth

    <link rel="stylesheet" href="{{ asset('/assets/css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/button.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Profile - {{ $user->name }}</title>

    <div class="profile-container">
        <link rel="stylesheet" href="resources/css/buttoncollection.css">
        <div class="profile-header">
            <h1>{{ $user->name }}</h1>
            <p>Joined on: {{ $user->created_at->format('d/m/Y') }}</p>
            <p>Images of {{ $user->name }}</p> <!-- Dòng chữ mới -->
                <!--route('collections.user', $user->id) }} -->
            <div class="collection" style="display: flex;
                                            align-items: center;
                                            justify-content: center;">
                <a href="{{ route('collections.user', $user->id) }}" class="collection-button">
                    View Collections
                </a>

                @if (auth()->id() !== $user->id)
                <form action="{{ route('room.create') }}" method="POST" style="display: inline; margin-left:20px">
                    @csrf
                    <input type="hidden" name="artist_id" value="{{ $user->id }}">
                    <button type="submit" class="chat-with-artist">
                        <div style="font-size: 40px">
                            <i class="fa-solid fa-comments"></i>
                        </div>
                    </button>
                </form>
            @endif


            </div>

        </div>

        <div class="profile-images">
            @if ($images->isEmpty())
                <p>User has not uploaded any images yet.</p>
            @else
            @foreach ($images as $image)
            <div class="image-thumbnail">
                <a href="{{ url('/images', $image->id) }}">
                    <img src="{{ asset($image->path) }}" alt="{{ $image->filename }}" style="max-width: 150px; height: auto;">
                </a>
            </div>
        @endforeach
            @endif
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="javascript:history.back()" class="back-button" style="padding: 10px 15px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">
                Return
            </a>
        </div>
    </div>

@endsection --}}



