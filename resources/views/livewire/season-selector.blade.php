<div>  
    <div class="mb-4">
        <!-- Season Dropdown -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <label for="season" class="text-white fw-bold">Season:</label>
            <select wire:model.change="selectedSeason" class="form-select w-auto">
                @foreach ($seasons as $season)
                    <option value="{{ $season }}">Season {{ $season }}</option>
                @endforeach
            </select>
        </div>

        {{-- @if (empty($filteredEpisodes))
            <div class="alert alert-warning text-dark">
                No episodes found for this content {{ $selectedSeason }}
            </div>
        @endif --}}

        <div class="d-flex flex-column gap-3 py-3">
            @foreach ($filteredEpisodes as $ep)
                <div class="d-flex bg-dark rounded p-3 align-items-start position-relative">
                    <img src="{{ $ep['THUMBNAIL_PATH'] 
                            ? 'http://15.184.102.5/SeePrime/Content/Images/' . $ep['THUMBNAIL_PATH'] 
                            : asset('images/default.jpg') }}"
                         alt="{{ $ep['TITLE'] ?? 'Episode' }}"
                         class="rounded" style="width: 180px; height: auto; object-fit: cover;">

                    <div class="ms-3 flex-grow-1">
                        <h6 class="fw-bold text-white mb-1">{{ $ep['TITLE'] ?? 'Untitled Episode' }}</h6>
                        <small class="text-muted d-block mb-1">
                            {{ $ep['RELEASE_DATE'] ?? '' }} • {{ $ep['DURATION'] ?? 'N/A' }}
                            @if (!empty($ep['AGE_RATING']))
                                <span class="badge bg-secondary ms-2">{{ $ep['AGE_RATING'] }}</span>
                            @endif
                        </small>
                        <p class="text-white small mb-2">
                            {{ $ep['DESCRIPTION'] ?? 'No description available.' }}
                        </p>
                    </div>

                    <div class="ms-3">
                        <a href="{{ route('play.video', ['id' => $contentId, 'partId' => $ep['CONTENT_DETAIL_ID']]) }}"
                            class="text-white fs-5" title="Watch Episode">
                            <i class="bi bi-play-circle"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
