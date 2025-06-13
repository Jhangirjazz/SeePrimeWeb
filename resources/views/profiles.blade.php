@extends('layouts.master')

@section('title', 'Select Profile')

@section('content')
<div class="container py-5 text-center">
    <h2 class="text-white mb-4">Who's watching?</h2>

    @php
    $profiles = [
        ['name' => 'Bilal', 'image' => 'screen1.jpg'],
        ['name' => 'Admin', 'image' => 'screen2.jpg'],
        ['name' => 'RCS', 'image' => 'screen3.jpg'],
        ['name' => 'Lisa', 'image' => 'screen4.jpg'],
    ];
    @endphp

    <div class="row justify-content-center">
        @foreach ($profiles as $index => $profile)
        <div class="col-md-2 col-6 mb-4">
            <a href="{{ url('/welcome') }}" class="text-decoration-none text-white profile-card">
                <div class="card bg-dark border-0 shadow-sm h-100 transition-hover">
                    <div class="position-relative">
                        <img src="{{ asset('images/' . $profile['image']) }}" 
                             alt="{{ $profile['name'] }}" 
                             class="card-img-top rounded-circle mx-auto d-block mt-3" 
                             style="height: 120px; width: 120px; object-fit: cover; border: 3px solid transparent;"
                             onmouseover="this.style.borderColor='#007bff'"
                             onmouseout="this.style.borderColor='transparent'">
                    </div>
                    <div class="card-body p-3 text-center">
                        <p class="mb-1 fw-bold text-white" style="color: white !important;">{{ $profile['name'] }}</p>
                        <i class="fas fa-pencil-alt text-muted small" style="color: #6c757d !important;"></i>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>

    <!-- Add Profile Button -->
    <div class="row justify-content-center mt-4">
        <div class="col-md-2 col-6">
            <a href="{{ url('/add-profile') }}" class="text-decoration-none text-white">
                <div class="card bg-transparent border-2 border-secondary text-center py-4">
                    <div class="card-body">
                        <i class="fas fa-plus fa-3x text-secondary mb-2"></i>
                        <p class="mb-0 text-secondary">Add Profile</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<style>
.transition-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.profile-card:hover .transition-hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0,123,255,0.3) !important;
}

.profile-card:hover {
    text-decoration: none !important;
}
</style>
@endsection