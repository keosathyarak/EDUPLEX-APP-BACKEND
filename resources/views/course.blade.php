@extends('layouts.app')

@section('title','Courses')
@section('page-title','Courses')

@section('content')

@if(session('success'))
<script>
Swal.fire({
toast:true,
position:'top-end',
icon:'success',
title:"{{ session('success') }}",
showConfirmButton:false,
timer:2500
});
</script>
@endif

@if($errors->any())
<script>
Swal.fire({
icon:'error',
title:'Validation Error',
html:`{!! implode('<br>',$errors->all()) !!}`
});
</script>
@endif

<div class="d-flex justify-content-between mb-3">
<h4>Course List</h4>
<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addModal">
<i class="bi bi-plus-circle"></i> Add Course
</button>
</div>

<div class="card shadow-sm">
<div class="card-body table-responsive">

<table class="table align-middle">
<thead class="table-light">
<tr>
<th>#</th>
<th>Image</th>
<th>Title</th>
<th>Teacher</th>
<th>Lessons</th>
<th>Price</th>
<th width="220">Action</th>
</tr>
</thead>
<tbody>

@foreach($courses as $course)
<tr>

<td>{{ $loop->iteration }}</td>

<td>
@if($course->image)
<img src="{{ asset('storage/'.$course->image) }}"
width="60" height="60"
style="object-fit:cover;border-radius:8px;">
@else
<span class="text-muted">No Image</span>
@endif
</td>

<td>{{ $course->title }}</td>
<td>{{ $course->teacher }}</td>

<td>
<span class="badge bg-primary">
{{ $course->lessons_count }} Lessons
</span>
</td>

<td>${{ $course->price }}</td>

<td>

<a href="{{ route('courses.lessons',$course->id) }}"
class="btn btn-sm btn-info">
<i class="bi bi-play-circle"></i>
</a>

<button class="btn btn-sm btn-warning editBtn"
data-id="{{ $course->id }}"
data-title="{{ $course->title }}"
data-teacher="{{ $course->teacher }}"
data-price="{{ $course->price }}"
data-bs-toggle="modal"
data-bs-target="#editModal">
<i class="bi bi-pencil"></i>
</button>

<form action="{{ route('courses.destroy',$course->id) }}"
method="POST"
class="d-inline delete-form">
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

</div>
</div>


{{-- ADD MODAL --}}
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">

<form action="{{ route('courses.store') }}"
method="POST"
enctype="multipart/form-data">
@csrf

<div class="modal-header">
<h5>Add Course</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Title</label>
<input type="text" name="title"
class="form-control" required>
</div>

<div class="mb-3">
<label>Teacher</label>
<input type="text" name="teacher"
class="form-control" required>
</div>

<div class="mb-3">
<label>Price</label>
<input type="number"
step="0.01"
name="price"
class="form-control">
</div>

<div class="mb-3">
<label>Image</label>
<input type="file"
name="image"
id="imageInput"
class="form-control">
<img id="previewImage"
class="mt-2 d-none rounded"
width="120">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary"
data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary">Save</button>
</div>

</form>
</div>
</div>
</div>


{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal">
<div class="modal-dialog">
<div class="modal-content">

<form id="editForm"
method="POST"
enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="modal-header">
<h5>Edit Course</h5>
<button class="btn-close"
data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="mb-3">
<label>Title</label>
<input type="text"
id="editTitle"
name="title"
class="form-control">
</div>

<div class="mb-3">
<label>Teacher</label>
<input type="text"
id="editTeacher"
name="teacher"
class="form-control">
</div>

<div class="mb-3">
<label>Price</label>
<input type="number"
step="0.01"
id="editPrice"
name="price"
class="form-control">
</div>

<div class="mb-3">
<label>Replace Image</label>
<input type="file"
name="image"
class="form-control">
</div>

</div>

<div class="modal-footer">
<button class="btn btn-secondary"
data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-success">Update</button>
</div>

</form>
</div>
</div>
</div>

@endsection


@push('scripts')
<script>

// Image Preview
document.getElementById('imageInput')
.addEventListener('change', function(e){
let reader = new FileReader();
reader.onload = function(){
let preview = document.getElementById('previewImage');
preview.src = reader.result;
preview.classList.remove('d-none');
}
reader.readAsDataURL(e.target.files[0]);
});

// Edit
document.querySelectorAll('.editBtn')
.forEach(button=>{
button.addEventListener('click',function(){
let id=this.dataset.id;
document.getElementById('editTitle').value=this.dataset.title;
document.getElementById('editTeacher').value=this.dataset.teacher;
document.getElementById('editPrice').value=this.dataset.price;
document.getElementById('editForm').action="/courses/"+id;
});
});

// Delete Confirm
document.querySelectorAll('.delete-form')
.forEach(form=>{
form.addEventListener('submit',function(e){
e.preventDefault();
Swal.fire({
title:'Delete this course?',
text:'All lessons will also be deleted!',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#dc3545'
}).then(result=>{
if(result.isConfirmed){
form.submit();
}
});
});
});

</script>
@endpush
