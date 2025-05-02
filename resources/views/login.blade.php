<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - SeePrime</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">
  <div class="login-overlay">
    <div class="login-form">

      <!-- SEEPRIME logo -->
      <img src="{{ asset('images/logo.png') }}" alt="SeePrime Logo">
      
      <!-- Form -->
      <form method="POST" action="{{url('/')}}">
        @csrf

        @if(session('error'))
            <div class="alert alert-danger" style="background-color: #ff3333; color: white; border-radius: 5px; padding: 10px; text-align: center; margin-bottom: 20px;">
        {{ session('error') }}
            </div>
        @endif

        <input type="text" name="username" placeholder="User Name" required>
        <input type="password" name="password" placeholder="password" required>
        @if($errors->any())
        <div class="text-danger mt-2">
          {{ $errors->first() }}
        </div>
      @endif
        <button type="submit">Sign In</button>
      </form>

      <!-- Forgot Password -->
      <a href="#" class="forgot-password">Forgot Password?</a>

      <!-- Create Account -->
      <div class="create-account">
        Don’t have an account? <a href={{url('/register')}}>Create one</a>
      </div>

    </div>
  </div>
</div>

</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>