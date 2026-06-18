@extends('layouts.app')

@section('title','Lessons')
@section('page-title','Lesson List')

@section('content')

@if(session('success'))
<script>
Swal.fire({
    toast:true,
    position:'top-end',
    icon:'success',
    title:"{{ session('success') }}",
    showConfirmButton:false,
    timer:2500,
    timerProgressBar:true
});
</script>
@endif

@if($errors->any())
<script>
Swal.fire({
    icon:'error',
    title:'Upload Error',
    html:`{!! implode('<br>',$errors->all()) !!}`
});
</script>
@endif

<h4 class="mb-3">
    {{ __('Course') }}: <strong>{{ $course->title }}</strong>
</h4>

<a href="{{ route('courses.index') }}" class="btn btn-secondary mb-3">
    ← {{ __('Back to Courses') }}
</a>

<button class="btn btn-primary mb-3"
        data-bs-toggle="modal"
        data-bs-target="#addLessonModal">
    <i class="bi bi-plus-circle"></i> {{ __('Add Lesson') }}
</button>

<div class="card shadow-sm">
    <div class="card-body">

        @if($course->lessons->count() > 0)

        <table class="table align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Video') }}</th>
                    <th width="180">{{ __('Action') }}</th>
                </tr>
            </thead>

            <tbody>
                @foreach($course->lessons as $lesson)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>
                        <strong>{{ $lesson->title }}</strong>
                    </td>

                    <td style="width:160px;">
                        @if($lesson->video)
                        <video width="140"
                               height="80"
                               class="rounded shadow-sm border"
                               controls
                               style="object-fit:cover;">
                            <source src="{{ asset('storage/'.$lesson->video) }}"
                                    type="video/mp4">
                            {{ __('Your browser does not support video.') }}
                        </video>
                        @else
                        <span class="text-muted">{{ __('No video') }}</span>
                        @endif
                    </td>

                    <td>
                        <button class="btn btn-sm btn-warning editLessonBtn"
                                data-id="{{ $lesson->id }}"
                                data-title="{{ $lesson->title }}"
                                data-bs-toggle="modal"
                                data-bs-target="#editLessonModal">
                            <i class="bi bi-pencil"></i>
                        </button>

                        <form action="{{ route('lessons.destroy',$lesson->id) }}"
                              method="POST"
                              class="d-inline deleteLesson">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @else

        <div class="text-center py-4">
            <i class="bi bi-collection-play"
               style="font-size:40px;color:#ccc;"></i>

            <p class="mt-2 text-muted">
                {{ __('No lessons available for this course.') }}
            </p>
        </div>

        @endif

    </div>
</div>

{{-- ADD LESSON MODAL --}}
<div class="modal fade" id="addLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="addLessonForm"
                  action="{{ route('lessons.store') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf

                <input type="hidden"
                       name="course_id"
                       value="{{ $course->id }}">

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add Lesson') }}</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ __('Lesson Title') }}</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Video File') }}</label>
                        <input type="file"
                               name="video"
                               id="addLessonVideo"
                               class="form-control"
                               accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm"
                               required>

                        <small class="text-muted">
                            {{ __('Support: MP4, MOV, AVI, MKV, WEBM') }}
                        </small>
                    </div>

                    <div id="addLessonProgress" class="d-none">
                        <div class="progress mb-1" style="height: 20px;">
                            <div id="addLessonProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                        <small class="text-muted d-block text-end" id="addLessonProgressText">0MB / 0MB</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit"
                            class="btn btn-success"
                            id="saveLessonBtn">
                        {{ __('Save') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- EDIT LESSON MODAL --}}
<div class="modal fade" id="editLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="editLessonForm"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Edit Lesson') }}</h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">{{ __('Lesson Title') }}</label>
                        <input type="text"
                               id="editLessonTitle"
                               name="title"
                               class="form-control"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Replace Video') }}</label>
                        <input type="file"
                               name="video"
                               id="editLessonVideo"
                               class="form-control"
                               accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">

                        <small class="text-muted">
                            {{ __('Leave empty if you do not want to replace video.') }}
                        </small>
                    </div>

                    <div id="editLessonProgress" class="d-none">
                        <div class="progress mb-1" style="height: 20px;">
                            <div id="editLessonProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                        <small class="text-muted d-block text-end" id="editLessonProgressText">0MB / 0MB</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit"
                            class="btn btn-warning"
                            id="updateLessonBtn">
                        {{ __('Update') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks

async function uploadFileInChunks(file, progressElements) {
    const { progressBar, progressText, progressContainer } = progressElements;
    const identifier = Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    const totalChunks = Math.ceil(file.size / CHUNK_SIZE);
    
    progressContainer.classList.remove('d-none');
    
    for (let i = 0; i < totalChunks; i++) {
        const start = i * CHUNK_SIZE;
        const end = Math.min(file.size, start + CHUNK_SIZE);
        const chunk = file.slice(start, end);
        
        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('chunkIndex', i);
        formData.append('totalChunks', totalChunks);
        formData.append('identifier', identifier);
        formData.append('_token', '{{ csrf_token() }}');

        try {
            const response = await fetch("{{ route('lessons.uploadChunk') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errData = await response.json().catch(() => ({}));
                throw new Error(errData.error || 'Chunk upload failed');
            }

            const result = await response.json();
            
            // Update progress
            const percent = Math.round(((i + 1) / totalChunks) * 100);
            progressBar.style.width = percent + '%';
            progressBar.innerText = percent + '%';
            
            const uploadedMB = (end / (1024 * 1024)).toFixed(2);
            const totalMB = (file.size / (1024 * 1024)).toFixed(2);
            progressText.innerText = `${uploadedMB}MB / ${totalMB}MB`;

            if (result.completed) {
                return result.path;
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Upload failed: ' + error.message, 'error');
            throw error;
        }
    }
}

const addLessonForm = document.getElementById('addLessonForm');
const editLessonForm = document.getElementById('editLessonForm');

if (addLessonForm) {
    addLessonForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('addLessonVideo');
        if (!fileInput.files.length) return;
        
        const file = fileInput.files[0];
        const saveBtn = document.getElementById('saveLessonBtn');
        saveBtn.disabled = true;

        try {
            const videoPath = await uploadFileInChunks(file, {
                progressBar: document.getElementById('addLessonProgressBar'),
                progressText: document.getElementById('addLessonProgressText'),
                progressContainer: document.getElementById('addLessonProgress')
            });

            // Finalize lesson creation
            const formData = new FormData(addLessonForm);
            formData.delete('video'); // Remove the file from final request
            formData.append('video_path', videoPath);

            const response = await fetch(addLessonForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                throw new Error(result.error || 'Failed to save lesson');
            }
        } catch (error) {
            saveBtn.disabled = false;
        }
    });
}

if (editLessonForm) {
    editLessonForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('editLessonVideo');
        const updateBtn = document.getElementById('updateLessonBtn');
        updateBtn.disabled = true;

        try {
            let videoPath = null;
            if (fileInput.files.length) {
                videoPath = await uploadFileInChunks(fileInput.files[0], {
                    progressBar: document.getElementById('editLessonProgressBar'),
                    progressText: document.getElementById('editLessonProgressText'),
                    progressContainer: document.getElementById('editLessonProgress')
                });
            }

            // Finalize lesson update
            const formData = new FormData(editLessonForm);
            formData.delete('video'); 
            if (videoPath) formData.append('video_path', videoPath);

            const response = await fetch(editLessonForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                throw new Error(result.error || 'Failed to update lesson');
            }
        } catch (error) {
            updateBtn.disabled = false;
        }
    });
}

document.querySelectorAll('.editLessonBtn').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;
        let title = this.dataset.title;

        document.getElementById('editLessonTitle').value = title;
        document.getElementById('editLessonForm').action =
            "{{ url('/lessons') }}/" + id;
    });
});

document.querySelectorAll('.deleteLesson').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        Swal.fire({
            title: "{{ __('Delete this lesson?') }}",
            text: "{{ __('This action cannot be undone!') }}",
            icon:'warning',
            showCancelButton:true,
            confirmButtonColor:'#dc3545',
            confirmButtonText: "{{ __('Yes, delete') }}"
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "{{ __('Deleting...') }}",
                    allowOutsideClick:false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                form.submit();
            }
        });
    });
});
</script>
@endpush
