@extends('layouts.app')

@section('title', __('Users'))
@section('page-title', __('User Management'))

@section('content')

<div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
    <h5 class="fw-bold mb-0">{{ __('Users') }}</h5>
    <button class="btn btn-primary" onclick="openAddModal()">
        <i class="bi bi-plus-circle"></i> {{ __('Add User') }}
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>{{ __('Profile') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Role') }}</th>
                <th class="text-center" style="width:160px;">{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody id="userTable"></tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3">
        <div id="paginationInfo" class="small text-muted"></div>
        <nav aria-label="User Pagination">
            <ul class="pagination pagination-sm mb-0" id="userPagination"></ul>
        </nav>
    </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5>{{ __('Add User') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input id="addName" class="form-control mb-2" placeholder="{{ __('Name') }}">
                <input id="addEmail" class="form-control mb-2" placeholder="{{ __('Email') }}">
                <select id="addRole" class="form-select mb-2">
                    <option value="user">{{ __('User') }}</option>
                    <option value="admin">{{ __('Admin') }}</option>
                </select>
                <div class="mb-2">
                    <label class="small text-muted">{{ __('Profile Picture') }}</label>
                    <input id="addProfilePicture" type="file" class="form-control">
                </div>
                <input id="addPassword" type="password" class="form-control" placeholder="{{ __('Password') }}">
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button class="btn btn-primary" onclick="addUser()">{{ __('Save') }}</button>
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
                <h5>{{ __('Edit User') }}</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <input id="editName" class="form-control mb-2" placeholder="{{ __('Name') }}">
                <input id="editEmail" class="form-control mb-2" placeholder="{{ __('Email') }}">

                <select id="editRole" class="form-select mb-2">
                    <option value="user">{{ __('User') }}</option>
                    <option value="admin">{{ __('Admin') }}</option>
                </select>

                <div class="mb-2">
                    <label class="small text-muted">{{ __('Replace Profile Picture') }}</label>
                    <input id="editProfilePicture" type="file" class="form-control">
                </div>

                <input id="editPassword" type="password" class="form-control"
                       placeholder="{{ __('New password (optional)') }}">
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button class="btn btn-success" onclick="updateUser()">{{ __('Update') }}</button>
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

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

const headers = {
    'Accept': 'application/json',
    'Authorization': 'Bearer ' + TOKEN,
    'X-CSRF-TOKEN': CSRF_TOKEN,
    'X-Requested-With': 'XMLHttpRequest'
};

const addModalEl = document.getElementById('addModal');
const editModalEl = document.getElementById('editModal');
const addModal = addModalEl ? new bootstrap.Modal(addModalEl) : null;
const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;


// ================= LOAD USERS =================
function loadUsers(page = 1) {
    Swal.fire({
        title: "{{ __('Loading...') }}",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(`${API}?page=${page}`, { headers })
        .then(res => {
            if (res.status === 401) throw new Error('UNAUTHENTICATED');
            if (res.status === 403) throw new Error('UNAUTHORIZED');
            return res.json();
        })
        .then(data => {
            Swal.close();

            const usersData = data.users;
            if (!usersData || !usersData.data) throw new Error('INVALID_DATA');

            let html = '';
            usersData.data.forEach((u, i) => {
                const startIndex = (usersData.current_page - 1) * usersData.per_page;
                let profileImg = u.profile_picture_url
                    ? `<img src="${u.profile_picture_url}" width="40" height="40" class="rounded-circle" style="object-fit:cover;">`
                    : `<div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">${u.name ? u.name.charAt(0) : 'U'}</div>`;

                html += `
                <tr>
                    <td>${startIndex + i + 1}</td>
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
            document.getElementById('paginationInfo').innerText = `{{ __('Showing') }} ${usersData.from} {{ __('to') }} ${usersData.to} {{ __('of') }} ${usersData.total} {{ __('users') }}`;
            
            renderPagination(usersData);
        })
        .catch(err => {
            Swal.close();
            if (err.message === 'UNAUTHENTICATED') {
                Swal.fire("{{ __('Session Expired') }}", "{{ __('Please login again.') }}", 'error')
                    .then(() => window.location.href = '/login');
            } else if (err.message === 'UNAUTHORIZED') {
                Swal.fire("{{ __('Access Denied') }}", "{{ __('Admin role required to view this page.') }}", 'error')
                    .then(() => window.location.href = '/');
            } else {
                Swal.fire("{{ __('Error') }}", "{{ __('Failed to load users. Please try again.') }}", 'error');
            }
        });
}

function renderPagination(data) {
    const pagination = document.getElementById('userPagination');
    let html = '';

    // Previous
    html += `<li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadUsers(${data.current_page - 1})">{{ __('Previous') }}</a>
    </li>`;

    // Pages
    for (let i = 1; i <= data.last_page; i++) {
        if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
            html += `<li class="page-item ${data.current_page === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadUsers(${i})">${i}</a>
            </li>`;
        } else if (i === data.current_page - 3 || i === data.current_page + 3) {
            html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }

    // Next
    html += `<li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="loadUsers(${data.current_page + 1})">{{ __('Next') }}</a>
    </li>`;

    pagination.innerHTML = html;
}


// ================= ADD USER =================
function openAddModal() {
    document.getElementById('addName').value = '';
    document.getElementById('addEmail').value = '';
    document.getElementById('addPassword').value = '';
    if (addModal) addModal.show();
}

function addUser() {
    const name = document.getElementById('addName').value;
    const email = document.getElementById('addEmail').value;
    const role = document.getElementById('addRole').value;
    const password = document.getElementById('addPassword').value;
    const profilePicture = document.getElementById('addProfilePicture').files[0];

    if (!name || !email || !password) {
        Swal.fire("{{ __('Error') }}", "{{ __('Please fill all fields') }}", 'error');
        return;
    }

    const formData = new FormData();
    formData.append('name', name);
    formData.append('email', email);
    formData.append('role', role);
    formData.append('password', password);
    formData.append('password_confirmation', password);
    if (profilePicture) {
        formData.append('profile_picture', profilePicture);
    }

    Swal.fire({
        title: "{{ __('Adding...') }}",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch('/api/adminregister', {
        method: 'POST',
        headers: headers,
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire("{{ __('Success') }}", "{{ __('User added successfully') }}", 'success');
            if (addModal) addModal.hide();
            loadUsers();
        } else {
            Swal.fire("{{ __('Error') }}", data.message ?? "{{ __('Failed to add user') }}", 'error');
        }
    })
    .catch(() => Swal.fire("{{ __('Error') }}", "{{ __('Server error') }}", 'error'));
}


// ================= EDIT =================
function openEdit(id) {
    fetch(API + '/' + id, { headers })
        .then(res => res.json())
        .then(r => {
            if (!r.user) {
                Swal.fire("{{ __('Error') }}", r.message || "{{ __('Unable to load user data') }}", 'error');
                return;
            }

            document.getElementById('editId').value = r.user.id;
            document.getElementById('editName').value = r.user.name || '';
            document.getElementById('editEmail').value = r.user.email || '';
            document.getElementById('editRole').value = r.user.role || 'user';
            document.getElementById('editPassword').value = '';
            if (editModal) editModal.show();
        })
        .catch(() => Swal.fire("{{ __('Error') }}", "{{ __('Unable to load user data') }}", 'error'));
}

function updateUser() {
    const id = document.getElementById('editId').value;
    const name = document.getElementById('editName').value;
    const email = document.getElementById('editEmail').value;
    const role = document.getElementById('editRole').value;
    const password = document.getElementById('editPassword').value;
    const profilePicture = document.getElementById('editProfilePicture').files[0];

    const formData = new FormData();
    formData.append('_method', 'PUT'); // Laravel handles PUT via POST + _method
    formData.append('name', name);
    formData.append('email', email);
    formData.append('role', role);
    if (password) {
        formData.append('password', password);
        formData.append('password_confirmation', password);
    }
    if (profilePicture) {
        formData.append('profile_picture', profilePicture);
    }

    Swal.fire({
        title: "{{ __('Updating...') }}",
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    fetch(API + '/' + id, {
        method: 'POST', // Use POST with _method PUT for file uploads
        headers,
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire("{{ __('Updated') }}", "{{ __('User updated successfully') }}", 'success');
            if (editModal) editModal.hide();
            loadUsers();
        } else {
            Swal.fire("{{ __('Error') }}", data.message ?? "{{ __('Failed to update user') }}", 'error');
        }
    })
    .catch(() => Swal.fire("{{ __('Error') }}", "{{ __('Failed to update user') }}", 'error'));
}


// ================= DELETE =================
function deleteUser(id) {
    Swal.fire({
        title: "{{ __('Are you sure?') }}",
        text: "{{ __('This user will be deleted permanently') }}",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: "{{ __('Yes, delete it!') }}"
    }).then((result) => {

        if (result.isConfirmed) {

            Swal.fire({
                title: "{{ __('Deleting...') }}",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(API + '/' + id, {
                method: 'DELETE',
                headers
            })
            .then(res => {
                if (res.status === 200) return res.json();
                throw new Error('DELETE_FAILED');
            })
            .then(data => {
                if (data.success) {
                     Swal.fire("{{ __('Deleted!') }}", "{{ __('User removed.') }}", 'success');
                     loadUsers();
                } else {
                     Swal.fire("{{ __('Error') }}", data.message || "{{ __('Failed to delete user') }}", 'error');
                }
            })
            .catch(() => {
                Swal.fire("{{ __('Error') }}", "{{ __('Failed to delete user') }}", 'error');
            });
        }
    });
}


// INIT
loadUsers();
</script>
@endpush
