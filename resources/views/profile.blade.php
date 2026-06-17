@extends('layouts.app')

@section('title', __('My Profile'))
@section('page-title', __('My Profile'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="bi bi-person-gear me-2"></i>{{ __('Profile Settings') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4 align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="position-relative d-inline-block">
                                    <img id="previewImage" src="{{ optional($user)->profile_picture_url ?? asset('images/default-avatar.png') }}" 
                                         class="rounded-circle border shadow-sm" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <label for="profile_picture" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle p-2 shadow">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*" onchange="previewFile()">
                                </div>
                                <div class="mt-2 small text-muted">{{ __('Click the camera icon to change') }}</div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label fw-semibold">{{ __('Full Name') }}</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3 text-secondary">{{ __('Change Password') }}</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label fw-semibold">{{ __('New Password') }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="{{ __('Leave blank to keep current') }}">
                                <small class="text-muted">{{ __('Minimum 8 characters') }}</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label fw-semibold">{{ __('Confirm New Password') }}</label>
                                <input type="password" class="form-control" 
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>{{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewFile() {
        const preview = document.getElementById('previewImage');
        const file = document.getElementById('profile_picture').files[0];
        const reader = new FileReader();

        reader.onloadend = function() {
            preview.src = reader.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
