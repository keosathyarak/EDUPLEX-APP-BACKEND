<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EduPlex Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function getApiToken() {
        const name = 'api_token=';
        const decodedCookie = decodeURIComponent(document.cookie);
        const ca = decodedCookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        
        const token = localStorage.getItem('token');
        if (token) {
            document.cookie = "api_token=" + encodeURIComponent(token) + "; path=/; SameSite=Lax";
            return token;
        }
        return '';
    }

    const existingToken = getApiToken();
    if (existingToken) {
        fetch('/api/user', {
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + existingToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.href = '/';
            } else {
                document.cookie = 'api_token=; path=/; max-age=0';
                localStorage.removeItem('token');
            }
        })
        .catch(() => {
            document.cookie = 'api_token=; path=/; max-age=0';
            localStorage.removeItem('token');
        });
    }
</script>

<style>
body{
    height:100vh;
    background: linear-gradient(135deg,#4f46e5,#9333ea,#ec4899);
    background-size:300% 300%;
    animation: gradientMove 8s ease infinite;
    display:flex;
    align-items:center;
    justify-content:center;
    font-family: 'Segoe UI', sans-serif;
}

@keyframes gradientMove{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

.login-card{
    width:400px;
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.15);
    border-radius:20px;
    padding:40px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
    color:#fff;
}

.login-card h2{
    font-weight:700;
}

.brand-logo{ display:flex; align-items:center; justify-content:center; gap:14px; }
.brand-logo svg{ width:72px; height:72px; flex-shrink:0; border-radius:12px; }
.brand-text{ font-size:28px; font-weight:800; color:#fff; letter-spacing:-0.5px; }


.form-control{
    border-radius:12px;
    background:rgba(255,255,255,0.2);
    border:none;
    color:#fff;
}

.form-control::placeholder{
    color:#eee;
}

.form-control:focus{
    background:rgba(255,255,255,0.3);
    box-shadow:none;
}

.btn-login{
    border-radius:12px;
    background:#fff;
    color:#4f46e5;
    font-weight:600;
    transition:0.3s;
}

.btn-login:hover{
    background:#e5e7eb;
}

.toggle-password{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#fff;
}
</style>
</head>
<body>

<div class="login-card text-center">

    <div class="brand-logo mb-3">
        <!-- Simple inline SVG EduPlex logo -->
        <svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
            <defs>
                <linearGradient id="g1" x1="0" x2="1">
                    <stop offset="0%" stop-color="#4f46e5"/>
                    <stop offset="50%" stop-color="#9333ea"/>
                    <stop offset="100%" stop-color="#ec4899"/>
                </linearGradient>
            </defs>
            <circle cx="60" cy="60" r="56" fill="url(#g1)" />
            <text x="60" y="72" text-anchor="middle" font-family="Segoe UI, sans-serif" font-weight="700" font-size="48" fill="#fff">EP</text>
        </svg>
        <div class="brand-text">EduPlex</div>
    </div>

    <p class="mb-4 text-white-50">Admin Login Portal</p>

    <div class="mb-3 text-start">
        <label>Email</label>
        <input type="email" id="email" class="form-control" placeholder="Enter email">
    </div>

    <div class="mb-4 position-relative text-start">
        <label>Password</label>
        <input type="password" id="password" class="form-control" placeholder="Enter password">
        <i class="bi bi-eye toggle-password" onclick="togglePassword()"></i>
    </div>

    <button class="btn btn-login w-100 mb-3" onclick="login()">
        Login
    </button>

    <small>© 2026 EduPlex System</small>

</div>

<script>

function togglePassword(){
    let pass = document.getElementById('password');
    let icon = document.querySelector('.toggle-password');

    if(pass.type === "password"){
        pass.type = "text";
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    }else{
        pass.type = "password";
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

function login(){

    Swal.fire({
        title:'Signing in...',
        allowOutsideClick:false,
        didOpen:()=>{ Swal.showLoading(); }
    });

    fetch('/api/login',{
        method:'POST',
        headers:{
            'Accept':'application/json',
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body:JSON.stringify({
            email:document.getElementById('email').value,
            password:document.getElementById('password').value
        }),
        credentials: 'include'
    })
    .then(res=>res.json())
    .then(data=>{

        Swal.close();

        if(data.token){

            localStorage.setItem('token',data.token);
            // The server already sends the cookie, but we can set it here too to be sure
            // and to make it accessible to JS if needed.
            document.cookie = 'api_token=' + encodeURIComponent(data.token) + '; path=/; SameSite=Lax';

            Swal.fire({
                icon:'success',
                title:'Welcome Back!',
                timer:1500,
                showConfirmButton:false
            }).then(()=>{
                window.location.href = '/';
            });

        }else{
            Swal.fire('Error',data.message ?? 'Login failed','error');
        }

    })
    .catch(()=>{
        Swal.fire('Error','Server error','error');
    });
}

</script>

</body>
</html>
