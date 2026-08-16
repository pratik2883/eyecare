<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Login — {{ config('app.name') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
:root{
  --charcoal:#1A1A1A;
  --cream:#F5F0EB;
  --plum:#C8102E;
  --plum-dark:#9A0C22;
  --gold:#D4AF37;
  --warm-white:#FFFDF9;
  --border:rgba(0,0,0,0.08);
  --font-serif:'Playfair Display',Georgia,serif;
  --font-sans:'Inter',-apple-system,sans-serif;
}
body{
  font-family:var(--font-sans);
  background:var(--cream);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
}
.login-container{
  width:100%;
  max-width:400px;
  padding:20px;
}
.login-card{
  background:var(--warm-white);
  border-radius:16px;
  box-shadow:0 8px 32px rgba(0,0,0,0.1);
  padding:40px 36px;
}
.login-brand{
  text-align:center;
  margin-bottom:32px;
}
.login-brand h1{
  font-family:var(--font-serif);
  font-size:1.4rem;
  letter-spacing:2px;
}
.login-brand .gem{color:var(--charcoal)}
.login-brand .opt{color:var(--gold)}
.login-brand small{
  display:block;
  font-size:.6rem;
  text-transform:uppercase;
  letter-spacing:3px;
  color:rgba(0,0,0,0.35);
  margin-top:4px;
}
.form-group{margin-bottom:20px}
.form-group label{
  display:block;
  font-size:.72rem;
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:1px;
  color:#666;
  margin-bottom:6px;
}
.form-control{
  width:100%;
  padding:10px 14px;
  border:1.5px solid var(--border);
  border-radius:8px;
  font-size:.88rem;
  background:var(--warm-white);
  color:var(--charcoal);
  font-family:var(--font-sans);
  transition:border-color .3s;
}
.form-control:focus{outline:none;border-color:var(--plum)}
.btn{
  width:100%;
  padding:11px;
  border:none;
  border-radius:8px;
  font-size:.85rem;
  font-weight:600;
  cursor:pointer;
  font-family:var(--font-sans);
  letter-spacing:.5px;
  transition:all .2s;
}
.btn-primary{background:var(--plum);color:var(--warm-white)}
.btn-primary:hover{background:var(--plum-dark)}
.error{
  background:#ffebee;
  color:#d43f3f;
  padding:10px 14px;
  border-radius:8px;
  font-size:.78rem;
  margin-bottom:16px;
}
.form-check{
  display:flex;
  align-items:center;
  gap:8px;
  margin-bottom:20px;
}
.form-check input{width:16px;height:16px;accent-color:var(--plum)}
.form-check label{font-size:.82rem;color:#666;cursor:pointer}
</style>
</head>
<body>
<div class="login-container">
  <div class="login-card">
    <div class="login-brand">
      <h1><span class="gem">EyeCare</span> <span class="opt">Studio</span></h1>
      <small>Admin Panel</small>
    </div>

    @if($errors->any())
    <div class="error"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
      @csrf
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="form-check">
        <input type="checkbox" name="remember" id="remember">
        <label for="remember">Stay signed in</label>
      </div>
      <button type="submit" class="btn btn-primary">Sign In</button>
    </form>
  </div>
</div>
</body>
</html>
