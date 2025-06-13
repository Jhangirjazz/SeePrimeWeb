@extends('layouts.master')

@section('title', 'My List')

@section('content')
<div class="container px-4 py-5">
    <h2 class="text-danger mb-4">My List</h2>

    @if (count($content) > 0)
        <div class="row">
            @foreach ($content as $item)
                @php
                    $thumb = $item['THUMBNAIL_PATH'] ?? null;
                    $source = strtolower($item['SOURCE'] ?? $item['SOURCE_PATH'] ?? '');
                    if ($thumb && str_ends_with($thumb, '.jpg')) {
                        $thumbUrl = "http://15.184.102.5:8443/SeePrime/Content/Images/{$thumb}";
                    } elseif ($source === 'youtube' && $thumb) {
                        $thumbUrl = "https://img.youtube.com/vi/{$thumb}/maxresdefault.jpg";
                    } else {
                        $thumbUrl = asset('images/default.jpg');
                    }
                @endphp

                <div class="col-md-3 col-sm-6 mb-4">
    <div class="card bg-dark text-white shadow">
        <a href="{{ route('play.video', ['id' => $item['CONTENT_ID']]) }}" class="text-decoration-none">
            <img src="{{ $thumbUrl }}" class="card-img-top" alt="{{ $item['TITLE'] }}">
        </a>
        <div class="card-body">
            <div class="d-flex flex-column align-items-center">
    <h5 class="card-title mb-2">{{ $item['TITLE'] }}</h5>

    <form method="POST" action="{{ route('mylist.remove') }}">
        @csrf
        <input type="hidden" name="content_id" value="{{ $item['CONTENT_ID'] }}">
        <button type="submit" class="btn btn-sm btn-danger">
            <i class="fas fa-trash-alt me-1"></i> Remove
        </button>
    </form>
</div>

        </div>
    </div>
</div>

            @endforeach
        </div>
    @else
        <p class="text-white">No items in your list yet.</p>
    @endif
</div>
@endsection
