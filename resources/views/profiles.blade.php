@extends('layouts.master')

@section('title', 'Select Profile')

@section('content')
<div class="container py-5 text-center">
    <h2 class="text-white mb-4">Who's watching?</h2>

    {{-- Alert messages --}}
    @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            {{ implode(', ', $errors->all()) }}
        </div>
    @endif

    {{-- Profile Cards --}}
    <div class="row justify-content-center">
        @if(isset($profiles) && is_array($profiles) && count($profiles) > 0)
            @foreach ($profiles as $profileItem)
            <div class="col-lg-2 col-md-3 col-6 mb-4">
                <div class="card bg-dark border-0 shadow-sm h-100 profile-card transition-hover">
                    <button type="button" class="bg-transparent border-0 w-100"
                        onclick="promptForPin('{{ $profileItem['ID'] ?? '' }}', '{{ $profileItem['PROFILE_PIN'] ?? '' }}')">
                        <img src="{{ isset($profileItem['PROFILE_PHOTO']) ? 'data:image/png;base64,' . $profileItem['PROFILE_PHOTO'] : asset('images/default-avatar.png') }}"
                            alt="{{ $profileItem['PROFILE_NAME'] ?? 'Profile' }}"
                            class="rounded-circle mt-3"
                            style="height: 120px; width: 120px; object-fit: cover; border: 3px solid transparent;"
                            onmouseover="this.style.borderColor='#007bff'"
                            onmouseout="this.style.borderColor='transparent'">
                        <div class="card-body text-white">
                            <p class="fw-bold mb-1">{{ $profileItem['PROFILE_NAME'] ?? 'Unnamed Profile' }}</p>
                            <small class="text-muted">{{ $profileItem['PROFILE_TYPE'] ?? '' }}</small>
                        </div>
                    </button>

                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-warning me-2"
                            onclick="editProfile(
                                '{{ $profileItem['ID'] ?? '' }}',
                                '{{ $profileItem['PROFILE_NAME'] ?? '' }}',
                                '{{ $profileItem['PROFILE_PIN'] ?? '' }}',
                                '{{ $profileItem['PROFILE_PHOTO'] ?? '' }}'
                            )">
                            Edit
                        </button>
                        
                        @if(!in_array($profileItem['PROFILE_TYPE'] ?? '', ['Adult', 'Kid']) || ($profileItem['DEFAULT_PROFILE'] ?? 'N') !== 'Y')
                            <form action="{{ route('profiles.delete', $profileItem['ID'] ?? '') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this profile?')">
                                    Delete
                                </button>
                            </form>
                        @else
                            <button class="btn btn-sm btn-secondary" disabled 
                                    title="Default {{ $profileItem['PROFILE_TYPE'] ?? '' }} profile cannot be deleted">
                                Delete
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    No profiles found. Please create a new profile below.
                </div>
            </div>
        @endif
    </div>

    {{-- Add Profile Button --}}
    <div class="row justify-content-center mt-4" id="showFormBtnContainer">
        <div class="col-auto">
            <button id="showFormBtn" class="btn btn-outline-light btn-lg" onclick="showProfileForm()">
                <i class="fas fa-plus me-2"></i>Create Profile
            </button>
        </div>
    </div>

    {{-- Add / Edit Form (Hidden by default) --}}
    <div class="row justify-content-center mt-5 {{ $errors->any() ? '' : 'd-none' }}" id="profileFormContainer">
        <div class="col-md-6 col-lg-4">
            <div class="card bg-dark p-4 text-white border-0 shadow">
                <form id="profileForm" action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="profile_id" id="profile_id">
                    <h4 class="mb-3" id="formTitle">Add New Profile</h4>

                    <div class="mb-3">
                        <input type="text" name="profile_name" id="profile_name" class="form-control" placeholder="Profile Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="profile_pin" id="profile_pin" class="form-control" placeholder="4-digit PIN" required maxlength="4">
                    </div>
                    <div class="mb-3">
                        <label for="profile_photo" class="form-label">Profile Image</label>
                        <input type="file" name="profile_photo" id="profile_photo" class="form-control" accept="image/*" required>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Create Profile</button>
                        <button type="button" class="btn btn-secondary" onclick="hideProfileForm()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.transition-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.profile-card:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0,123,255,0.3) !important;
}
</style>

@push('scripts')
<script>
// Show form when page loads if there are validation errors
document.addEventListener('DOMContentLoaded', function() {
    @if($errors->any())
        showProfileForm();
    @endif
});

function showProfileForm() {
    document.getElementById('profileFormContainer').classList.remove('d-none');
    document.getElementById('showFormBtnContainer').classList.add('d-none');
    resetForm();
}

function hideProfileForm() {
    document.getElementById('profileFormContainer').classList.add('d-none');
    document.getElementById('showFormBtnContainer').classList.remove('d-none');
    resetForm();
}

function resetForm() {
    document.getElementById('profile_id').value = '';
    document.getElementById('profile_name').value = '';
    document.getElementById('profile_pin').value = '';
    document.getElementById('profile_photo').required = true;
    document.getElementById('profile_photo').value = '';
    document.getElementById('submitBtn').innerText = 'Create Profile';
    document.getElementById('formTitle').innerText = 'Add New Profile';
}

function editProfile(id, name, pin, photo) {
    // Show the form first
    document.getElementById('profileFormContainer').classList.remove('d-none');
    document.getElementById('showFormBtnContainer').classList.add('d-none');
    
    // Then populate the form
    document.getElementById('profile_id').value = id;
    document.getElementById('profile_name').value = name;
    document.getElementById('profile_pin').value = pin;
    document.getElementById('profile_photo').required = false;
    document.getElementById('submitBtn').innerText = 'Update Profile';
    document.getElementById('formTitle').innerText = 'Edit Profile';
}

function promptForPin(profileId, correctPin) {
    const userPin = prompt("Enter 4-digit PIN:");
    if (userPin === null) return;

    if (`${userPin}` === `${correctPin}`) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("profile.login") }}';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'profile_id';
        input.value = profileId;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    } else {
        alert("Incorrect PIN.");
    }
}
</script>
@endpush
@endsection