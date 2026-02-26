@extends('layouts.app')

@section('title','Users')
@section('page-title','User Management')

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
    <h5 class="fw-bold mb-0">Users</h5>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-circle"></i> Add User
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Profile</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th class="text-center" style="width:160px;">Actions</th>
            </tr>
            </thead>
            <tbody id="userTable"></tbody>
        </table>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Add User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input id="addName" class="form-control mb-2" placeholder="Name">
                <input id="addEmail" class="form-control mb-2" placeholder="Email">
                <input id="addPassword" type="password" class="form-control" placeholder="Password">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="addUser()">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <input type="hidden" id="editId">

            <div class="modal-header">
                <h5>Edit User</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input id="editName" class="form-control mb-2">
                <input id="editEmail" class="form-control mb-2">

                <select id="editRole" class="form-select mb-2">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>

                <input id="editPassword" type="password" class="form-control"
                       placeholder="New password (optional)">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" onclick="updateUser()">Update</button>
            </div>

        </div>
    </div>
</div>

@endsection


@push('scripts')
<script>
const API = '/api/users';

// ✅ Get token from localStorage (AFTER LOGIN)
const TOKEN = localStorage.getItem('token');

// ✅ If no token → redirect or show alert
if (!TOKEN) {
    Swal.fire('Login required', 'No token found. Please login again.', 'warning')
        .then(() => window.location.href = '/login');
}

const headers = {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    'Authorization': 'Bearer ' + TOKEN
};

const addModal = new bootstrap.Modal(document.getElementById('addModal'));
const editModal = new bootstrap.Modal(document.getElementById('editModal'));


// ================= LOAD USERS =================
function loadUsers() {
    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(API, { headers })
        .then(res => res.json())
        .then(data => {
            Swal.close();

            if (!data.users) {
                Swal.fire('Unauthorized', 'Admin access required', 'error');
                return;
            }

            let html = '';
            data.users.forEach((u, i) => {

                let profileImg = u.profile_picture_url
                    ? `<img src="${u.profile_picture_url}" width="40" height="40" class="rounded-circle" style="object-fit:cover;">`
                    : `<div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">${u.name ? u.name.charAt(0) : 'U'}</div>`;

                html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${profileImg}</td>
                    <td>${u.name ?? ''}</td>
                    <td>${u.email}</td>
                    <td>
                        <span class="badge ${u.role === 'admin' ? 'bg-danger' : 'bg-primary'}">
                            ${u.role}
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-warning me-1" onclick="openEdit(${u.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
            });

            document.getElementById('userTable').innerHTML = html;
        })
        .catch(() => {
            Swal.fire('Error', 'Failed to load users', 'error');
        });
}


// ================= ADD USER =================
function openAddModal() {
    addName.value = '';
    addEmail.value = '';
    addPassword.value = '';
    addModal.show();
}

function addUser() {
    Swal.fire({
        title: 'Adding...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('/api/adminregister', {
        method: 'POST',
        headers,
        body: JSON.stringify({
            name: addName.value,
            email: addEmail.value,
            password: addPassword.value,
            password_confirmation: addPassword.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', 'User added successfully', 'success');
            addModal.hide();
            loadUsers();
        } else {
            Swal.fire('Error', data.message ?? 'Failed to add user', 'error');
        }
    })
    .catch(() => Swal.fire('Error', 'Server error', 'error'));
}


// ================= EDIT =================
function openEdit(id) {
    fetch(API + '/' + id, { headers })
        .then(res => res.json())
        .then(r => {
            editId.value = r.user.id;
            editName.value = r.user.name;
            editEmail.value = r.user.email;
            editRole.value = r.user.role;
            editPassword.value = '';
            editModal.show();
        });
}

function updateUser() {
    Swal.fire({
        title: 'Updating...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(API + '/' + editId.value, {
        method: 'PUT',
        headers,
        body: JSON.stringify({
            name: editName.value,
            email: editEmail.value,
            role: editRole.value,
            password: editPassword.value
        })
    })
    .then(res => res.json())
    .then(() => {
        Swal.fire('Updated', 'User updated successfully', 'success');
        editModal.hide();
        loadUsers();
    })
    .catch(() => Swal.fire('Error', 'Failed to update user', 'error'));
}


// ================= DELETE =================
function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This user will be deleted permanently',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: 'Deleting...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(API + '/' + id, {
                method: 'DELETE',
                headers
            }).then(() => {
                Swal.fire('Deleted!', 'User removed.', 'success');
                loadUsers();
            });
        }
    });
}


// INIT
loadUsers();
</script>
@endpush
