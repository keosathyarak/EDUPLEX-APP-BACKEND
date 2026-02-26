@extends('layouts.app')

@section('title','Lessons')
@section('page-title','Lesson List')

@section('content')

{{-- ================= SUCCESS TOAST ================= --}}
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

{{-- ================= VALIDATION ERROR ================= --}}
@if($errors->any())
<script>
Swal.fire({
    icon:'error',
    title:'Validation Error',
    html:`{!! implode('<br>',$errors->all()) !!}`
});
</script>
@endif

<h4 class="mb-3">
Course: <strong>{{ $course->title }}</strong>
</h4>

<a href="{{ route('courses.index') }}"
class="btn btn-secondary mb-3">
← Back to Courses
</a>

<button class="btn btn-primary mb-3"
data-bs-toggle="modal"
data-bs-target="#addLessonModal">
<i class="bi bi-plus-circle"></i> Add Lesson
</button>

<div class="card shadow-sm">
<div class="card-body">

@if($course->lessons->count() > 0)

<table class="table align-middle">
<thead class="table-light">
<tr>
<th>#</th>
<th>Title</th>
<th>Video</th>
<th width="180">Action</th>
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
<video width="140"
height="80"
class="rounded shadow-sm border"
controls
style="object-fit:cover;">
<source src="{{ asset('storage/'.$lesson->video) }}"
type="video/mp4">
</video>
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
<button class="btn btn-sm btn-danger">
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
No lessons available for this course.
</p>
</div>

@endif

</div>
</div>

{{-- ================= ADD LESSON MODAL ================= --}}
<div class="modal fade" id="addLessonModal">
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
<h5>Add Lesson</h5>
<button class="btn-close"
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Lesson Title</label>
<input type="text"
name="title"
class="form-control"
required>
</div>

<div class="mb-3">
<label>Video File</label>
<input type="file"
name="video"
class="form-control"
required>
</div>

</div>

<div class="modal-footer">
<button type="submit"
class="btn btn-success"
id="saveLessonBtn">
Save
</button>
</div>

</form>
</div>
</div>
</div>

{{-- ================= EDIT LESSON MODAL ================= --}}
<div class="modal fade" id="editLessonModal">
<div class="modal-dialog">
<div class="modal-content">

<form id="editLessonForm"
method="POST"
enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="modal-header">
<h5>Edit Lesson</h5>
<button class="btn-close"
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Lesson Title</label>
<input type="text"
id="editLessonTitle"
name="title"
class="form-control"
required>
</div>

<div class="mb-3">
<label>Replace Video (optional)</label>
<input type="file"
name="video"
class="form-control">
</div>

</div>

<div class="modal-footer">
<button type="submit"
class="btn btn-warning"
id="updateLessonBtn">
Update
</button>
</div>

</form>
</div>
</div>
</div>

@endsection


@push('scripts')
<script>

// ================= ADD LOADING =================
document.getElementById('addLessonForm')
.addEventListener('submit', function(){

Swal.fire({
title:'Uploading...',
text:'Please wait while video uploads',
allowOutsideClick:false,
didOpen:()=>{
Swal.showLoading();
}
});

document.getElementById('saveLessonBtn').disabled = true;

});

// ================= UPDATE LOADING =================
document.getElementById('editLessonForm')
.addEventListener('submit', function(){

Swal.fire({
title:'Updating...',
text:'Please wait...',
allowOutsideClick:false,
didOpen:()=>{
Swal.showLoading();
}
});

document.getElementById('updateLessonBtn').disabled = true;

});

// ================= EDIT BUTTON =================
document.querySelectorAll('.editLessonBtn')
.forEach(btn=>{
btn.addEventListener('click',function(){

let id=this.dataset.id;
let title=this.dataset.title;

document.getElementById('editLessonTitle').value=title;
document.getElementById('editLessonForm').action=
"{{ url('/lessons') }}/"+id;

});
});

// ================= DELETE CONFIRM =================
document.querySelectorAll('.deleteLesson')
.forEach(form=>{
form.addEventListener('submit',function(e){
e.preventDefault();

Swal.fire({
title:'Delete this lesson?',
text:'This action cannot be undone!',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#dc3545',
confirmButtonText:'Yes, delete'
}).then(result=>{
if(result.isConfirmed){

Swal.fire({
title:'Deleting...',
allowOutsideClick:false,
didOpen:()=>{
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
