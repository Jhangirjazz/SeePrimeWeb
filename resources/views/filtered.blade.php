@extends('layouts.master')

@section('title', $filterType . ' Based Results')

@section('content')
<div class="container py-4 mt-5 pt-5"> {{-- <-- ADD mt-5 pt-5 here --}}
    <h4 class="text-danger mb-4">{{ $filterType }} Based Results</h4>

    <div class="d-flex flex-wrap gap-3">
        @forelse ($content as $item)
            @php
                $thumbUrl = $item['thumb_url'] ?? asset('images/default.jpg');
            @endphp

            <div style="width: 200px; height: 500px;">
                <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}" class="text-decoration-none text-light">
                    <div class="position-relative">
                        <img src="{{ $thumbUrl }}" class="rounded w-100 shadow" alt="{{ $item['TITLE'] }}">
                        @if (!empty($item['IS_PREMIUM']) && $item['IS_PREMIUM'] === 'Y')
                            <span class="premium-badge position-absolute top-0 start-0 bg-danger px-2 py-1 text-white">Prime</span>
                        @endif
                    </div>
                    <p class="text-white mt-2 small fw-bold text-center">{{ $item['TITLE'] }}</p>
                </a>
            </div>
        @empty
            <p class="text-warning">No content found for this {{ strtolower($filterType) }}.</p>
        @endforelse
    </div>
</div>
@endsection
