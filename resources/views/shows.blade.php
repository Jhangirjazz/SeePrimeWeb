@extends('layouts.master')

@section('Title', 'Shows' )


@section('content')
<div class="video-wrapper">
    <video width="100%" height="100%" autoplay muted loop>
      <source src="{{ asset('videos/sample3.mp4') }}" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  
    <!-- Overlay -->
    <div class="video-overlay">
      <h2>Welcome to Shows</h2>
    </div>
  </div>

@endsection
