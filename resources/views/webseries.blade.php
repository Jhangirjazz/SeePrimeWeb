@extends('layouts.master')

@section('Title', 'Webseries' )


@section('content')
<div class="video-wrapper">
    <video width="100%" height="100%" autoplay muted loop>
      <source src="{{ asset('videos/sample3.mp4') }}" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  
    <!-- Overlay -->
    <div class="video-overlay">
      <h2>Welcome to webseries</h2>
    </div>
  </div>

@endsection
