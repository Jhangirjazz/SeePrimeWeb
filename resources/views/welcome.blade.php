<link rel="stylesheet" href="{{ asset('css/welcome.css') }}">

@extends('layouts.master')

@section('title','Home Page')

@section('content')

<div class="video-wrapper">
    <video width="100%" height="100%" autoplay muted loop>
      <source src="{{ asset('videos/sample2.mp4') }}" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  
    <!-- Overlay -->
    <div class="video-overlay">
      <h2>Welcome to SEEPRIME</h2>
    </div>
  </div>
  

@endsection