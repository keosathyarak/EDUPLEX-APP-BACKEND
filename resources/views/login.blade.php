<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EduPlex Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

    <h2>EduPlex</h2>
    <p class="mb-4">Admin Login Portal</p>

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
            'Content-Type':'application/json'
        },
        body:JSON.stringify({
            email:document.getElementById('email').value,
            password:document.getElementById('password').value
        })
    })
    .then(res=>res.json())
    .then(data=>{

        Swal.close();

        if(data.token){

            localStorage.setItem('token',data.token);

            Swal.fire({
                icon:'success',
                title:'Welcome Back!',
                timer:1500,
                showConfirmButton:false
            }).then(()=>{
                window.location.href="/user";
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
